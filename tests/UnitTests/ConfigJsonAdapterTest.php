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
 * Date: 11/26/18 - 8:35 AM
 */

namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Config\AbstractConfigObject;
use Geeshoe\DbLib\Config\ConfigJsonAdapter;
use Geeshoe\DbLib\Exceptions\DbLibException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigJsonAdapterTest
 *
 * @package Geeshoe\DbLibTests
 */
class ConfigJsonAdapterTest extends TestCase
{
    /**
     * @var vfsStream|null
     */
    public $stream;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->stream = vfsStream::setup('configTest');
        vfsStream::newFile('config1', 0000)->at($this->stream);
        vfsStream::newFile('config2')->at($this->stream);
        vfsStream::newFile('config3')->at($this->stream);
        vfsStream::newFile('config4')->at($this->stream);
        file_put_contents('vfs://configTest/config2', '{"someConfig": {}}');
    }

    /**
     * Data provider for testValidateConfigFileThrowsExceptions
     *
     * @return array
     */
    public function validateConfigFileExceptions(): array
    {
        return [
            [
                '/path/to/nowhere',
                'Specified config file does not exists for DbLib.'
            ],
            [
                'vfs://configTest/config1',
                'Json config file is not readable by DbLib.'
            ],
            [
                'vfs://configTest/config2',
                'DbLib Json config file is malformed.'
            ]
        ];
    }

    /**
     * @dataProvider validateConfigFileExceptions
     *
     * @param string $filePath
     * @param string $exceptionMsg
     */
    public function testValidateConfigFileThrowsExceptions(string $filePath, string $exceptionMsg): void
    {
        $config = new ConfigJsonAdapter($filePath);
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage($exceptionMsg);
        $config->initialize();
    }

    /**
     * Data provider for testValidateConfigObjectThrowsExceptions.
     *
     * @return array
     */
    public function validateConfigObjectDataProvider(): array
    {
        return [
            'Hostname' => ['hostName', '{"dblibConfig":{}}'],
            'Port' => ['port', '{"dblibConfig":{"hostName": "host"}}'],
            'Database' => ['database', '{"dblibConfig":{"hostName": "host","port":12}}'],
            'Username' => ['userName', '{"dblibConfig":{"hostName": "host","port":12,"database":"db"}}'],
            'Password' => ['password', '{"dblibConfig":{"hostName": "host","port":12,"database":"db","userName":"user"}}'],
        ];
    }

    /**
     * @dataProvider validateConfigObjectDataProvider
     *
     * @param string $field
     * @param string $json
     */
    public function testValidateConfigObjectThrowsExceptions(string $field, string $json): void
    {
        file_put_contents('vfs://configTest/config3', $json);
        $config = new ConfigJsonAdapter('vfs://configTest/config3');
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('DbLib Json Adapter requires ' . $field . ' to be set in the config file.');
        $config->initialize();
    }

    /**
     * Data provider for testGetParamsReturnsParamsFromJsonConfigFile
     *
     * @return array
     */
    public function goodParamsDataProvider(): array
    {
        return [
            ['host', 'host'],
            ['port', 12],
            ['database', 'db'],
            ['user', 'user'],
            ['password', 'pass']
        ];
    }
    /**
     * If the config file is valid, test everything works as intended.
     *
     * @dataProvider goodParamsDataProvider
     *
     * @param string $property
     * @param mixed $key
     */
    public function testGetParamsReturnsParamsFromJsonConfigFile(string $property, $key): void
    {
        file_put_contents('vfs://configTest/config4', '{"dblibConfig":{"hostName": "host","port":12,"database":"db","userName":"user","password":"pass"}}');
        $config = new ConfigJsonAdapter('vfs://configTest/config4');
        $config->initialize();
        $results = $config->getParams();

        $this->assertSame($key, $results->$property);
        $this->assertInstanceOf(AbstractConfigObject::class, $results);
    }
}
