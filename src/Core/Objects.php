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
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @param string $sqlStmt
     * @param string $className
     *
     * @return object
     *
     * @throws DbLibQueryException
     */
    public function queryDBGetSingleResultAsClass(string $sqlStmt, string $className): object
    {
        $query = $this->connection->query($sqlStmt, \PDO::FETCH_CLASS, $className);

        if ($query === false) {
            throw new DbLibQueryException(
                'Query returned false. Check SQL syntax and/or class name.'
            );
        }

        $result = $query->fetch();
        $query->closeCursor();

        if ($result === false) {
            throw new DbLibQueryException(
                'Fetch returned false. Requested record does not exist.'
            );
        }

        return $result;
    }
}
