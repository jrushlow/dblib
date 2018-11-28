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
 * Date: 11/27/18 - 5:38 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Core;

use Geeshoe\DbLib\Config\AbstractConfigObject;
use Geeshoe\DbLib\Exceptions\DbLibException;

/**
 * Class AbstractConnection
 *
 * @package Geeshoe\DbLib\Core
 */
abstract class AbstractConnection
{
    /**
     * @var AbstractConfigObject
     */
    protected $credentials;

    /**
     * Create a PDO Connection
     *
     * @return \PDO
     */
    protected function getConnection(): \PDO
    {
        $dsn = 'mysql:host=' . $this->credentials->host;

        if ($this->credentials->database !== null) {
            $dsn .= ';dbname=' . $this->credentials->database;
        }

        try {
            $connection = new \PDO($dsn, $this->credentials->user, $this->credentials->password);
        } catch (\PDOException $exception) {
            throw new DbLibException('Connection error', $exception->getCode(), $exception);
        }

        return $connection;
    }
}
