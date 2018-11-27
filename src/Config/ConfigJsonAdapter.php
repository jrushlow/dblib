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
 * Date: 11/26/18 - 8:21 AM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Config;

use Geeshoe\DbLib\Exceptions\DbLibException;

/**
 * Class ConfigJsonAdapter
 *
 * @package Geeshoe\DbLib\Config
 */
class ConfigJsonAdapter extends AbstractConfigObject
{
    /**
     * @var null|string
     */
    protected $filePath;

    /**
     * @var object|null
     */
    protected $jsonObject;

    /**
     * ConfigJsonAdapter constructor.
     *
     * @param string $jsonConfigFilePath
     */
    public function __construct(string $jsonConfigFilePath)
    {
        $this->filePath = $jsonConfigFilePath;
    }

    /**
     * Verify the config file exists, is readable, and valid json.
     */
    protected function validateConfigFile(): void
    {
        switch ($this->filePath) {
            case !is_file($this->filePath):
                throw new DbLibException(
                    'Specified config file does not exists for DbLib.'
                );
                break;
            case !is_readable($this->filePath):
                throw new DbLibException(
                    'Json config file is not readable by DbLib.'
                );
                break;
        }

        $jsonConfig = json_decode(file_get_contents($this->filePath));

        if (empty($jsonConfig->dblibConfig) || !\is_object($jsonConfig->dblibConfig)) {
            throw new DbLibException('DbLib Json config file is malformed.');
        }

        $this->jsonObject = $jsonConfig->dblibConfig;
    }

    /**
     * Verify that all the required database params are available.
     */
    protected function validateConfigObject(): void
    {
        $fields = ['hostName', 'port', 'database', 'userName', 'password'];

        foreach ($fields as $field) {
            if (empty($this->jsonObject->$field)) {
                throw new DbLibException(
                    'DbLib Json Adapter requires ' . $field . ' to be set in the config file.'
                );
            }
        }
    }

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
    ): void {
        $this->host = filter_var($host, FILTER_SANITIZE_URL);
        $this->port = (int) filter_var($port, FILTER_VALIDATE_INT);
        $this->database = filter_var($database, FILTER_SANITIZE_STRING);
        $this->user = filter_var($user, FILTER_SANITIZE_STRING);
        $this->password = filter_var($password, FILTER_SANITIZE_STRING);
    }

    /**
     * Entry point to parse the json config file and return the required values.
     *
     * @return array
     */
    public function getParams(): array
    {
        $this->validateConfigFile();
        $this->validateConfigObject();
        $this->setParams(
            $this->jsonObject->hostName,
            $this->jsonObject->port,
            $this->jsonObject->database,
            $this->jsonObject->userName,
            $this->jsonObject->password
        );

        return parent::getParams();
    }
}
