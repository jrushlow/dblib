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
abstract class AbstractConfigObject
{
    /**
     * @var string|null
     */
    public $host;

    /**
     * @var int|null
     */
    public $port;

    /**
     * @var string|null
     */
    public $database;

    /**
     * @var string|null
     */
    public $user;

    /**
     * @var string|null
     */
    public $password;

    /**
     * Methodology is to initialize the configuration at the start of the
     * application. Then call getParams() when needed within the application to
     * reduce overhead.
     */
    abstract protected function initialize(): void;

    /**
     * @return AbstractConfigObject
     */
    public function getParams(): self
    {
        return $this;
    }
}
