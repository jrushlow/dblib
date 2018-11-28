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
 * Date: 11/27/18 - 9:00 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Config;

use Geeshoe\DbLib\Exceptions\DbLibException;

/**
 * Class PDOAttributeAdapter
 *
 * @package Geeshoe\DbLib\Config
 */
class PDOAttributeAdapter extends AbstractAttributeObject
{
    /**
     * @var array|null ['Attribute' => 'Value']
     */
    protected $attributes = null;

    /**
     * @var null|\PDO
     */
    protected $connection = null;

    /**
     * PDOAttributeAdapter constructor.
     *
     * @param \PDO $connection Established PDO Connection
     * @param array $attributes
     */
    public function __construct(\PDO $connection, array $attributes)
    {
        $this->connection = $connection;
        $this->attributes = $attributes;
    }

    /**
     * Check and set PDO Attributes on an established connection.
     *
     * {@inheritdoc}
     */
    public function setAttribute(): \PDO
    {
        foreach ($this->attributes as $attribute => $value) {
            $this->name = filter_var($attribute, FILTER_SANITIZE_STRING);
            $this->value = filter_var($value, FILTER_SANITIZE_STRING);

            if (!$this->checkAttribute()) {
                throw new DbLibException(
                    'PDO Attributes '. $this->name . ' - ' . $this->value . ' are invalid.'
                );
            }

            $this->connection->setAttribute(\constant($this->name), \constant($this->value));
        }

        return $this->connection;
    }
}
