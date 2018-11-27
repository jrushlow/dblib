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
 * Date: 11/26/18 - 7:39 AM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Config;

/**
 * Interface ConfigInterface
 *
 * @package Geeshoe\DbLib\Config
 */
interface ConfigInterface
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $user
     * @param string $password
     */
    public function setParams(
        string $host,
        int $port,
        string $database,
        string $user,
        string $password
    ): void;
}
