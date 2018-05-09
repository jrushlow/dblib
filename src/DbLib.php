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
namespace Geeshoe\DbLib;


/**
 * Class DbLib
 * @package Geeshoe\DbLib
 */
class DbLib
{
    /**
     * @var null
     */
    private $connection = null;

    /**
     * @var null
     */
    private $iniPath = null;

    /**
     * @var array
     */
    public $insert = array();

    /**
     * @var array
     */
    public $values = array();

    /**
     * DbLib constructor.
     * @param $iniLocation
     */
    public function __construct($iniLocation)
    {
        $this->iniPath = $iniLocation;
    }

    /**
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
     * @param $sqlStatement
     */
    public function executeQueryWithNoReturn($sqlStatement)
    {
        $this->connect()->exec($sqlStatement);
    }

    /**
     * @param $sqlStatement
     * @param int $fetchStyle
     * @return mixed
     */
    public function executeQueryWithSingleReturn($sqlStatement, int $fetchStyle)
    {
        $result = $this->connect()->query($sqlStatement)->fetch($fetchStyle);
        return $result;
    }

    /**
     * @param $sqlStatement
     * @param int $fetchStyle
     * @return array
     */
    public function executeQueryWithAllReturned($sqlStatement, int $fetchStyle)
    {
        $result = $this->connect()->query($sqlStatement)->fetchAll($fetchStyle);
        return $result;
    }

    /**
     * @param $sqlStatement
     * @param $valuesArray
     */
    public function manipulateDataWithNoReturn($sqlStatement, $valuesArray)
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
    }

    /**
     * @param string $sqlStatement
     * @param array $valuesArray
     * @param int $fetchStyle
     * @return mixed
     */
    public function manipulateDataWithSingleReturn(
        string $sqlStatement,
        array $valuesArray,
        int $fetchStyle
    )
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetch($fetchStyle);
        return $results;
    }

    /**
     * @param string $sqlStatement
     * @param array $valuesArray
     * @param int $fetchStyle
     * @return array
     */
    public function manipulateDataWithAllReturned(
        string $sqlStatement,
        array $valuesArray,
        int $fetchStyle)
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetchAll($fetchStyle);
        return $results;
    }

    /**
     *
     */
    public function deleteData()
    {
    }

    /**
     * @param string $typeOfArray
     * @param array $userSuppliedData
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
     * @param string $insertInWhatTable
     * @return string
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
     * @param string $updateWhatTable
     * @param string $updateByWhatColumn
     * @param string $updateWhatId
     * @return string
     */
    public function createSqlUpdateStatement(
        string $updateWhatTable,
        string $updateByWhatColumn,
        string $updateWhatId
    )
    {
        return 'UPDATE `'.$updateWhatTable.'` SET ' . implode(", ", $this->insert) . ' WHERE `'
            .$updateByWhatColumn.'` = ' . $updateWhatId;
    }

    /**
     * @param string $deleteFromWhichTable
     * @param string $deleteByWhatColumn
     * @param string $deleteWhatId
     * @return string
     */
    public function createSqlDeleteStatement(
        string $deleteFromWhichTable,
        string $deleteByWhatColumn,
        string $deleteWhatId
    )
    {
        return 'DELETE FROM `' . $deleteFromWhichTable . '` WHERE `'
            . $deleteByWhatColumn . '` = ' . $deleteWhatId . ';';
    }
}
