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
 * Date: 11/27/18 - 8:29 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Config;

/**
 * Class AbstractAttributeObject
 *
 * @package Geeshoe\DbLib\Config
 */
abstract class AbstractAttributeObject
{
    /**
     * @var string|null
     */
    protected $name = null;

    /**
     * @var string|null
     */
    protected $value = null;

    /**
     * @return bool
     */
    protected function checkAttribute(): bool
    {
        return \defined($this->name) && \defined($this->value);
    }

    /**
     * Implement method to set Attributes.
     *
     * I.e. \PDO->setAttribute($this->name, $this->value);
     */
    abstract protected function setAttribute(): \PDO;
}
