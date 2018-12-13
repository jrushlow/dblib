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

/**
 * Class AbstractConnection
 *
 * @package Geeshoe\DbLib\Core
 */
abstract class AbstractConnection
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * AbstractConnection constructor.
     *
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }
}
