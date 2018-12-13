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
 * Date: 12/12/18 - 10:39 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Core;

use Geeshoe\DbLib\Exceptions\DbLibQueryException;

/**
 * Class Objects
 *
 * @package Geeshoe\DbLib\Core
 */
class Objects extends AbstractConnection
{
    /**
     * Check if a query was successfully executed on the database.
     *
     * @param string $sqlStmt       SQL statement to query. 'SELECT * FROM table;'
     * @param string $className     Class to be returned as a result. SomeClass::class
     *
     * @return \PDOStatement
     *
     * @throws DbLibQueryException  Thrown if \PDO::query fails to execute a statement.
     */
    protected function makeQuery(string $sqlStmt, string $className): \PDOStatement
    {
        $statement = $this->connection->query($sqlStmt, \PDO::FETCH_CLASS, $className);

        if ($statement === false) {
            throw new DbLibQueryException(
                'PDO::query failed to execute statement. Check SQL syntax and/or class name.'
            );
        }

        return $statement;
    }

    /**
     * Check if the query executed returned a result.
     *
     * @param mixed $result         After calling $query->fetch*(); The result is either bool|object.
     *
     * @return array|object         Returns either an array of objects or a single object.
     *
     * @throws DbLibQueryException
     */
    protected function checkResultForFailure($result)
    {
        if ($result === false) {
            throw new DbLibQueryException(
                '\PDOStatement::fetch returned false. Requested record does not exist.'
            );
        }

        return $result;
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Executes a SQL statement returning a single object as the result.
     *
     * This method is not safe to use with untrusted data. Use prepared statements instead.
     * Method will return a single object upon success, or throw an exception is no result
     * is found.
     *
     * @param string $sqlStmt       SQL statement to be executed. 'SELECT * FROM table;'.
     * @param string $className     Class to be returned as a result. SomeClass::class
     *
     * @return object               User defined object
     *
     * @throws DbLibQueryException  Thrown if \PDO::query fails to execute a statement or
     *                              if no results were found upon executing the query. I.e.
     *                              \PDOStatement::fetch() returns false.
     */
    public function queryDBGetSingleResultAsClass(string $sqlStmt, string $className): object
    {
        $query = $this->makeQuery($sqlStmt, $className);

        $result = $query->fetch();
        $query->closeCursor();

        return $this->checkResultForFailure($result);
    }

    /**
     * Executes a SQL statement returning an array of objects.
     *
     * This method is not safe to use with untrusted data. Use prepared statements instead.
     * Method will return either an array of predefined objects i.e. [MyObject::class, MyObject::class],
     * or an empty array if no results exist.
     *
     * @param string $sqlStmt       SQL statement to be executed. 'SELECT * FROM table;'.
     * @param string $className     Class to be returned as a result. SomeClass::class
     *
     * @return array                Returns an array of objects [Object1::class, Object1::class]
     *                              or an empty array if no results exist.
     *
     * @throws DbLibQueryException  Thrown if \PDO::query fails to execute a statement.
     */
    public function queryDbGetAllResultsAsClass(string $sqlStmt, string $className): array
    {
        $query = $this->makeQuery($sqlStmt, $className);

        $result = $query->fetchAll();
        $query->closeCursor();

        return $result;
    }
}
