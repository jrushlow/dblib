<?php
/*
 * Copyright 2018 Jesse Rushlow - Geeshoe Development

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */
declare(strict_types=1);

namespace Geeshoe\DbLib;

/**
 * Class DbLib
 * @package Geeshoe\DbLib
 */
class DbLib
{
    /**
     * @var null|\PDO
     */
    private $connection = null;

    /**
     * @var null|string
     */
    private $iniPath = null;

    /**
     * @var array Populated by the create methods below.
     */
    public $insert = array();

    /**
     * @var array Populated by the create methods below.
     */
    public $values = array();

    /**
     * DbLib constructor.
     *
     * @param string $iniLocation Absolute path to config file.
     */
    public function __construct(string $iniLocation)
    {
        $this->iniPath = $iniLocation;
    }

    /**
     * Parses the config file and creates a new PDO instance.
     *
     * @return null|\PDO
     */
    private function connect()
    {
        if (!isset($this->connection)) {
            $ini = parse_ini_file($this->iniPath, true);
            $this->connection = new \PDO(
                'mysql:dbname='.$ini['mysql']['dataBase'].
                ';host='.$ini['mysql']['hostName'].':'.$ini['mysql']['port'],
                $ini['mysql']['userName'],
                $ini['mysql']['passWord']
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->connection;
    }

    /**
     * Execute a statement without returning any affected row's.
     *
     * Useful for issuing command's to the server.
     *
     * @param string $sqlStatement
     */
    public function executeQueryWithNoReturn(string $sqlStatement)
    {
        $this->connect()->exec($sqlStatement);
    }

    /**
     * Execute a query, returning 1 single affected row.
     *
     * I.e. 'SELECT * FROM `clients` WHERE `name` = jesse;
     * Note: Use manipulateDataWithSingleReturn when query is used in conjunction
     * with untrusted user supplied data. I.e. Form data...
     *
     * @param string $sqlStatement
     * @param int $fetchStyle
     * @return mixed
     */
    public function executeQueryWithSingleReturn(string $sqlStatement, int $fetchStyle)
    {
        $result = $this->connect()->query($sqlStatement)->fetch($fetchStyle);
        return $result;
    }

    /**
     * Execute a query returning 1 or more affected row's.
     *
     * I.e. 'SELECT * FROM `clients`;
     *
     * @param string $sqlStatement
     * @param int $fetchStyle
     * @return array
     */
    public function executeQueryWithAllReturned(string $sqlStatement, int $fetchStyle)
    {
        $result = $this->connect()->query($sqlStatement)->fetchAll($fetchStyle);
        return $result;
    }

    /**
     * Execute a prepared statement without returning any affected rows.
     *
     * I.e. 'DELETE FROM `myClients` WHERE `name` = :name'
     * It was intended to use the manipulateData methods in conjunction with the
     * create methods below. See documentation for further details and examples.
     *
     * @param string $sqlStatement
     * @param array $valuesArray
     */
    public function manipulateDataWithNoReturn(string $sqlStatement, array $valuesArray)
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
    }

    /**
     * Execute a prepared statement returning only 1 affected row.
     *
     * I.e. 'SELECT * FROM `myClients` WHERE `clientId` = :id';
     * It was intended to use the manipulateData methods in conjunction with the
     * create methods below. See documentation for further details and examples.
     *
     * @param string $sqlStatement
     * @param array $valuesArray
     * @param int $fetchStyle
     * @return mixed
     */
    public function manipulateDataWithSingleReturn(
        string $sqlStatement,
        array $valuesArray,
        int $fetchStyle
    ) {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetch($fetchStyle);
        return $results;
    }

    /**
     * Execute a prepared statement returning one or more affected rows.
     *
     * I.e. 'SELECT * FROM `myClients` WHERE `city` = :city';
     * It was intended to use the manipulateData methods in conjunction with the
     * create methods below. See documentation for further details and examples.
     *
     * @param string $sqlStatement
     * @param array $valuesArray
     * @param int $fetchStyle
     * @return array
     */
    public function manipulateDataWithAllReturned(
        string $sqlStatement,
        array $valuesArray,
        int $fetchStyle
    ) {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetchAll($fetchStyle);
        return $results;
    }

    /**
     * Creates an array of data to be used in conjunction with the manipulate methods.
     *
     * When the method is called, it populates both the insert and values properties.
     * Use insert in the typeOfArray argument when creating new rows, otherwise
     * use manipulate.
     *
     * See documentation for further examples and use cases.
     *
     * @param string $typeOfArray Value should be either "insert" or "manipulate"
     * @param array $userSuppliedData
     *
     * @return void
     */
    public function createDataArray(string $typeOfArray, array $userSuppliedData)
    {
        foreach (array_keys($userSuppliedData) as $key) {
            if ($typeOfArray == 'insert') {
                $this->insert[] = $key;
            } elseif ($typeOfArray == 'manipulate') {
                $this->insert[] = '`' . $key . '`' . ' = :' . $key;
            }
            //@TODO - Throw exception if wrong $typeOfStatement is entered.
            $this->values[':'.$key] = $userSuppliedData[$key];
        }
    }

    /**
     * Creates a insert statement. Must call the createDataArray method first!
     *
     * See documentation for further examples and use cases.
     *
     * @param string $insertInWhatTable
     *
     * @return string Returns a query statement to be used for the manipulate methods.
     */
    public function createSqlInsertStatement(string $insertInWhatTable)
    {
        $statement = 'INSERT INTO `'.$insertInWhatTable.'`('
            . implode(', ', $this->insert) .
            ') VALUE ('
            . implode(', ', array_keys($this->values)) .
            ')';
        return $statement;
    }

    /**
     * Creates an update statement. Must call the createDataArray method first!
     *
     * See documentation for further examples and use cases.
     *
     * @param string $updateWhatTable
     * @param string $updateByWhatColumn
     * @param string $updateWhatId
     *
     * @return string Returns a query statement to be used for the manipulate methods.
     */
    public function createSqlUpdateStatement(
        string $updateWhatTable,
        string $updateByWhatColumn,
        string $updateWhatId
    ) {
        return 'UPDATE `'.$updateWhatTable.'` SET ' . implode(", ", $this->insert) . ' WHERE `'
            .$updateByWhatColumn.'` = ' . $updateWhatId;
    }

    /**
     * Creates a delete statement. Must call the createDataArray method first!
     *
     * See documentation for further examples and use cases.
     *
     * @param string $deleteFromWhichTable
     * @param string $deleteByWhatColumn
     * @param string $deleteWhatId
     *
     * @return string Returns a query statement to be used for the manipulate methods.
     */
    public function createSqlDeleteStatement(
        string $deleteFromWhichTable,
        string $deleteByWhatColumn,
        string $deleteWhatId
    ) {
        return 'DELETE FROM `' . $deleteFromWhichTable . '` WHERE `'
            . $deleteByWhatColumn . '` = ' . $deleteWhatId . ';';
    }
}
