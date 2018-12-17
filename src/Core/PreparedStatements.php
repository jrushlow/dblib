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

use Geeshoe\DbLib\Data\Statements;
use Geeshoe\DbLib\Exceptions\DbLibException;

/**
 * Class PreparedStatements
 *
 * @package Geeshoe\DbLib\Core
 */
class PreparedStatements
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * PreparedStatements constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param \PDOStatement $statement
     * @param string        $placeHolder
     * @param               $value
     */
    protected function bindValue(\PDOStatement $statement, string $placeHolder, $value): void
    {
        if ($statement->bindValue($placeHolder, $value) === false) {
            throw new DbLibException(
                'Failed to bind value using Placeholder: (' . $placeHolder . ') Value: (' . $value . ').'
            );
        }
    }

    /**
     * @param \Exception|null $exception
     */
    protected function prepareException(\Exception $exception = null): void
    {
        throw new DbLibException(
            'Database is unable to prepare statement.',
            0,
            $exception
        );
    }

    /**
     * @param string $sqlStatement
     * @return \PDOStatement
     */
    protected function prepareStatement(string $sqlStatement): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sqlStatement);
        } catch (\PDOException $exception) {
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
     * @param string $table             Table name used by SQL Database.
     * @param array  $userSuppliedData ['MySQL_Column' => 'Sanitized_Value']
     */
    public function executePreparedInsertQuery(string $table, array $userSuppliedData): void
    {
        if (empty($userSuppliedData)) {
            throw new DbLibException(
                '$userSuppliedData array must not be empty.'
            );
        }

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
}
