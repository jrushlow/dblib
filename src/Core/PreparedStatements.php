<?php
/**
 * Copyright 2018 Jesse Rushlow - Geeshoe Development
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * User: Jesse Rushlow - Geeshoe Development
 * Date: 12/16/18 - 7:05 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Core;

use Exception;
use Geeshoe\DbLib\Data\Statements;
use Geeshoe\DbLib\Exceptions\DbLibException;
use Geeshoe\DbLib\Exceptions\DbLibQueryException;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class PreparedStatements
 *
 * @package Geeshoe\DbLib\Core
 */
class PreparedStatements
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * PreparedStatements constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array $dataArray
     *
     * @throws DbLibException
     */
    protected function checkDataArrayValid(array $dataArray): void
    {
        if (empty($dataArray)) {
            throw new DbLibException(
                '$userSuppliedData array must not be empty.'
            );
        }
    }

    /**
     * @param PDOStatement $statement
     *
     * @return PDOStatement
     *
     * @throws DbLibException
     * @throws PDOException
     */
    protected function executeStmt(PDOStatement $statement): PDOStatement
    {
        if (!$statement->execute()) {
            throw new DbLibException(
                'Failed to execute prepared statement.'
            );
        }

        return $statement;
    }

    /**
     * @param PDOStatement $statement
     * @param string        $placeHolder
     * @param               $value
     * @throws DbLibException
     */
    protected function bindValue(PDOStatement $statement, string $placeHolder, $value): void
    {
        if ($statement->bindValue($placeHolder, $value) === false) {
            throw new DbLibException(
                'Failed to bind value using Placeholder: (' . $placeHolder . ') Value: (' . $value . ').'
            );
        }
    }

    /**
     * @param Exception|null $exception
     * @throws DbLibException
     */
    protected function prepareException(Exception $exception = null): void
    {
        throw new DbLibException(
            'Database is unable to prepare statement.',
            0,
            $exception
        );
    }

    /**
     * @param string $sqlStatement
     * @return PDOStatement
     * @throws DbLibException
     * @throws DbLibException
     */
    protected function prepareStatement(string $sqlStatement): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sqlStatement);
        } catch (PDOException $exception) {
            $this->prepareException($exception);
        }

        if ($stmt === false) {
            $this->prepareException();
        }

        return $stmt;
    }

    /**
     * Insert data into table using prepared statements.
     *
     * Caution, this method does not sanitize nor validate and data. That
     * is your responsibility. It does however, insert a new row into a
     * table by means of using prepared statements.
     *
     * @param string $table            Table name used by SQL Database.
     * @param array  $userSuppliedData ['MySQL_Column' => 'Sanitized_Value']
     * @throws DbLibException
     * @throws PDOException
     * @throws DbLibException
     * @throws DbLibException
     * @throws DbLibException
     */
    public function executePreparedInsertQuery(string $table, array $userSuppliedData): void
    {
        $this->checkDataArrayValid($userSuppliedData);

        $parsedDataArray = Statements::prepareInsertQueryData($table, $userSuppliedData);

        $stmt = $this->prepareStatement($parsedDataArray['sql']);

        foreach ($parsedDataArray['values'] as $placeHolder => $value) {
            $this->bindValue($stmt, $placeHolder, $value);
        }

        if (!$stmt->execute()) {
            throw new DbLibException(
                'Failed to execute prepared statement.'
            );
        }
    }

    /**
     * Bind values to the supplied SQL statement, execute prepared statement, and
     * return the result as a class.
     *
     * As this method is unable to predict what the sqlStatement will be, the
     * supplied key to each value within the data array will be parsed and used
     * as the placeholder when preparing the SQL statement. Therefor, you must
     * format the SQL statement accordingly. See the param examples below.
     *
     * @param string $sqlStatement     'SELECT * FROM table WHERE MySQL_Column => :MySQL_Column;'
     * @param array  $userSuppliedData ['MySQL_Column' => 'Sanitized_Value']
     * @param string $className        NameOfClass::class
     *
     * @return object
     *
     * @throws DbLibQueryException
     * @throws DbLibException
     * @throws PDOException
     */
    public function executePreparedFetchAsClass(
        string $sqlStatement,
        array $userSuppliedData,
        string $className
    ): object {
        $stmt = $this->executePreparedPreFetch($sqlStatement, $userSuppliedData);

        $stmt->setFetchMode(PDO::FETCH_CLASS, $className);

        $result = $stmt->fetch();

        if ($result === false) {
            throw new DbLibQueryException(
                'PDO::fetch() failed to retrieve a result.'
            );
        }

        return $result;
    }

    /**
     * Bind values to the supplied SQL statement, execute prepared statement, and
     * returns the results as an array of class's or an empty array if no results
     * were fetched.
     *
     * @param string $sqlStatement
     * @param array  $userSuppliedData
     * @param string $className
     *
     * @return array
     *
     * @throws DbLibException
     * @throws PDOException
     */
    public function executePreparedFetchAllAsClass(
        string $sqlStatement,
        array $userSuppliedData,
        string $className
    ): array {
        $stmt = $this->executePreparedPreFetch($sqlStatement, $userSuppliedData);

        $stmt->setFetchMode(PDO::FETCH_CLASS, $className);

        return $stmt->fetchAll();
    }

    /**
     * @param string $sqlStatement
     * @param array  $userSuppliedData
     *
     * @return PDOStatement
     *
     * @throws DbLibException
     * @throws PDOException
     */
    protected function executePreparedPreFetch(
        string $sqlStatement,
        array $userSuppliedData
    ): PDOStatement {
        $this->checkDataArrayValid($userSuppliedData);

        $values = Statements::getValuesArray($userSuppliedData);

        $stmt = $this->prepareStatement($sqlStatement);

        foreach ($values as $placeHolder => $value) {
            $this->bindValue($stmt, $placeHolder, $value);
        }

        return $this->executeStmt($stmt);
    }
}
