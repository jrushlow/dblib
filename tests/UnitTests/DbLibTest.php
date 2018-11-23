<?php
/**
 * Copyright 2018 Geeshoe Development Services
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */
namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Core\DbLib;
use Geeshoe\DbLib\Exceptions\DbLibException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DbLibTest extends TestCase
{
    public function invokeMethod(&$object, $methodName, array $params = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $params);
    }

    public function testConnectThrowsExceptionIfConfigFileIsUnavailable()
    {
        $db = new DbLib('/some/dblib/path/to/config.json');

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('Specified config file location does not exists for DbLib.');
        $this->invokeMethod($db, 'connect');
    }

    public function setConfigFile()
    {
        vfsStream::setup('config');
        $file = vfsStream::url('config/dbconfig.json');
        return $file;
    }

    public function testConnectThrowsExceptionWhenConfigFileIsMalformed()
    {
        $file = $this->setConfigFile();
        file_put_contents(
            $file,
            '{
                      "MalformedDbConfig" : {
                      }
                    }'
        );

        $db = new DbLib($file);

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('DbLib config file malformed.');

        $this->invokeMethod($db, 'connect');
    }

    public function testDbLibConfigMethodParsesConfigFile()
    {
        $file = $this->setConfigFile();
        file_put_contents(
            $file,
            '{
                      "dblibConfig" : {
                        "hostName" : "127.0.0.1",
                        "port" : "3306",
                        "username" : "myUsername",
                        "password" : "SomePassword",
                        "database" : "someDatabase",
                        "pdoAttributes" : [
                          {
                            "attribute" : "PDO::ATTR_CASE",
                            "value" : "PDO::CASE_NATURAL"
                          },
                          {
                            "attribute" : "PDO::ATTR_ERRMODE",
                            "value" : "PDO::ERRMODE_EXCEPTION"
                          }
                        ]
                      }
                    }'
        );
        $db = new DbLib($file);
        $this->expectException(DbLibException::class);
        $test = $this->invokeMethod($db, 'connect');
        self::assertInstanceOf(\PDO::class, $test);
    }

    public function configDataProvider()
    {
        return [
            'Missing Host Name' => ['{"dblibConfig" : {"hostName" : "127.0.0.1"}}', '\Exception']
        ];
    }

    public function testConnectThrowsExceptionWhenHostConfigParamIsNotSet()
    {
        $file = $this->setConfigFile();
        file_put_contents($file, '{"dblibConfig" : {"hostName" : ""}}');

        $db = new DbLib($file);
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('hostName is not set in the DbLib config file.');
        $this->invokeMethod($db, 'connect');
    }

    public function testConnectThrowsExceptionWhenPortConfigParamIsNotSet()
    {
        $file = $this->setConfigFile();
        file_put_contents($file, '{"dblibConfig" : {"hostName" : "1","port":""}}');

        $db = new DbLib($file);
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('port is not set in the DbLib config file.');
        $this->invokeMethod($db, 'connect');
    }

    public function testConnectThrowsExceptionWhenUsernameConfigParamIsNotSet()
    {
        $file = $this->setConfigFile();
        file_put_contents(
            $file,
            '{"dblibConfig":{"hostName":"1","port":"1","username":""}}'
        );

        $db = new DbLib($file);
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('username is not set in the DbLib config file.');
        $this->invokeMethod($db, 'connect');
    }

    public function testConnectThrowsExceptionWhenPasswordConfigParamIsNotSet()
    {
        $file = $this->setConfigFile();
        file_put_contents(
            $file,
            '{"dblibConfig":{"hostName":"1","port":"1","username":"u","password":""}}'
        );

        $db = new DbLib($file);
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('password is not set in the DbLib config file.');
        $this->invokeMethod($db, 'connect');
    }

    public function testCreateDataArrayCreatesInsertArray()
    {
        $db = new DbLib('some/path');
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];

        $insertArray = ['Col1','Col2'];
        $valuesArray = [':Col1' => 'Value1', ':Col2' =>'Value2'];

        $db->createDataArray('insert', $inputArray);

        self::assertSame($insertArray, $db->insert);
        self::assertSame($valuesArray, $db->values);
    }

    public function testCreateDataArrayCreatesManipulateArray()
    {
        $db = new DbLib('some/path');
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];

        $insertArray = ['`Col1` = :Col1','`Col2` = :Col2'];
        $valuesArray = [':Col1' => 'Value1', ':Col2' =>'Value2'];

        $db->createDataArray('manipulate', $inputArray);

        self::assertSame($insertArray, $db->insert);
        self::assertSame($valuesArray, $db->values);
    }

    public function testCreateSqlInsertStatementReturnsSQLStatement()
    {
        $db = new DbLib('some/path');
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];
        $db->createDataArray('insert', $inputArray);
        $stmt = $db->createSqlInsertStatement('TestingTable');
        self::assertSame(
            'INSERT INTO `TestingTable`(Col1, Col2) VALUE (:Col1, :Col2)',
            $stmt
        );
    }

    public function testCreateSqlUpdateStatementReturnsSQLStatement()
    {
        $db = new DbLib('some/path');
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];
        $db->createDataArray('manipulate', $inputArray);
        $stmt = $db->createSqlUpdateStatement(
            'TestingTable',
            'Col2',
            ':id'
        );

        self::assertSame(
            'UPDATE `TestingTable` SET `Col1` = :Col1, `Col2` = :Col2 WHERE `Col2` = :id',
            $stmt
        );
    }

    public function testCreateSqlDeleteStatementReturnsSQLStatement()
    {
        $db = new DbLib('some/path');

        $stmt = $db->createSqlDeleteStatement(
            'SomeTable',
            'Col1',
            ':id'
        );

        self::assertSame(
            'DELETE FROM `SomeTable` WHERE `Col1` = :id;',
            $stmt
        );
    }
}
