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

use Geeshoe\DbLib\Config\ConfigJsonAdapter;
use Geeshoe\DbLib\Core\DbLib;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DbLibTest extends TestCase
{
    public $stream;

    /**
     * @var DbLib
     */
    public $db;

    public function setUp()
    {
        $this->stream = vfsStream::setup('db');
        vfsStream::newFile('config')->at($this->stream);
        file_put_contents('vfs://db/config', '{"dblibConfig":{"hostName": "host","port":12,"database":"db","userName":"user","password":"pass"}}');
        $config = new ConfigJsonAdapter('vfs://db/config');
        $this->db = new DbLib($config->getParams());
    }

    public function testCreateDataArrayCreatesInsertArray()
    {
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];

        $insertArray = ['Col1','Col2'];
        $valuesArray = [':Col1' => 'Value1', ':Col2' =>'Value2'];

        $this->db->createDataArray('insert', $inputArray);

        self::assertSame($insertArray, $this->db->insert);
        self::assertSame($valuesArray, $this->db->values);
    }

    public function testCreateDataArrayCreatesManipulateArray()
    {
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];

        $insertArray = ['`Col1` = :Col1','`Col2` = :Col2'];
        $valuesArray = [':Col1' => 'Value1', ':Col2' =>'Value2'];

        $this->db->createDataArray('manipulate', $inputArray);

        self::assertSame($insertArray, $this->db->insert);
        self::assertSame($valuesArray, $this->db->values);
    }

    public function testCreateSqlInsertStatementReturnsSQLStatement()
    {
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];
        $this->db->createDataArray('insert', $inputArray);
        $stmt = $this->db->createSqlInsertStatement('TestingTable');
        self::assertSame(
            'INSERT INTO `TestingTable`(Col1, Col2) VALUE (:Col1, :Col2)',
            $stmt
        );
    }

    public function testCreateSqlUpdateStatementReturnsSQLStatement()
    {
        $inputArray = [
            'Col1'=>'Value1',
            'Col2'=>'Value2'
        ];
        $this->db->createDataArray('manipulate', $inputArray);
        $stmt = $this->db->createSqlUpdateStatement(
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

        $stmt = $this->db->createSqlDeleteStatement(
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
