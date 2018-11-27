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
 * Date: 11/26/18 - 7:37 AM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Config;

/**
 * Class AbstractConfigObject
 *
 * @package Geeshoe\DbLib\Config
 */
abstract class AbstractConfigObject implements ConfigInterface
{
    /**
     * @var string|null
     */
    protected $host;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string|null
     */
    protected $database;

    /**
     * @var string|null
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $user
     * @param string $password
     */
    abstract public function setParams(
        string $host,
        int $port,
        string $database,
        string $user,
        string $password
    ): void;

    /**
     * @return array Database param's from json config file.
     */
    public function getParams(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'user' => $this->user,
            'password' => $this->password
        ];
    }
}
