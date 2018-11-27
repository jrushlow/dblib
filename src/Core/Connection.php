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

use Geeshoe\DbLib\Exceptions\DbLibException;

/**
 * Class Connection
 *
 * @package Geeshoe\DbLib\Core
 */
class Connection
{
    /**
     * @var array|null
     */
    protected $credentials = null;

    /**
     * @var \PDO|null
     */
    protected $connection = null;

    /**
     * Connection constructor.
     *
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     *
     */
    protected function connect(): void
    {
        $dsn = 'mysql:=' . $this->credentials['host'];

        if (!empty($this->credentials['database'])) {
            $dsn .= ';database=' . $this->credentials['database'];
        }

        //@todo is there a better way to do this.
        try {
            if ($this->credentials['attributes']['persistent']) {
                $this->connection = new \PDO(
                    $dsn, $this->credentials['user'],
                    $this->credentials['password'],
                    [\PDO::ATTR_PERSISTENT => true]
                );
            } else {
                $this->connection = new \PDO($dsn, $this->credentials['user'], $this->credentials['password']);
            }
        } catch (\PDOException $exception) {
            throw new DbLibException('Connection error', $exception->getCode(), $exception);
        }
    }

    /**
     *
     */
    public function setAttributes(): void
    {
        $attributes = $this->connection['attributes'];

        foreach ($attributes as $key => $value) {
            switch ($key) {
                case 'persistent':
                    break;
                case !empty($attributes[$key]):
                    $this->connection->setAttribute(\constant($key), \constant($value));
                    break;
            }
        }
    }

    /**
     * @return null|\PDO
     */
    public function getConnection(): ?\PDO
    {
        $this->connect();
        $this->setAttributes();
        return $this->connection;
    }
}
