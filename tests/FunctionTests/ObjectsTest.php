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
 * Date: 12/12/18 - 10:51 PM
 */

namespace Geeshoe\DbLibTests\FunctionTests;

use Geeshoe\DbLib\Core\Objects;
use Geeshoe\DbLib\Exceptions\DbLibQueryException;
use Geeshoe\DbLib\TestObject1;
use PHPUnit\Framework\TestCase;

class ObjectsTest extends TestCase
{
    /**
     * @var Objects
     */
    public $db;

    /**
     * @var \PDO
     */
    public $pdo;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $pdo = new \PDO('mysql:host=' . HOST . ';port=' . PORT, USER, PASS);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `dblibTest`');
        $pdo->exec('CREATE TABLE IF NOT EXISTS dblibTest.test(
                              row1 INT PRIMARY KEY,
                              row2 INT
                  )');

        $this->pdo = $pdo;

        $pdo->exec('USE dblibTest;');
        $this->db = new Objects($pdo);
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->pdo->exec('DROP DATABASE `dblibTest`');
        $this->pdo = null;
    }

    public function testExecQryGetSingleObject(): void
    {
        $this->pdo->exec('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2;');

        $result = $this->db->queryDBGetSingleResultAsClass(
            'SELECT * FROM test WHERE row1 = 1',
            TestObject1::class
        );

        $this->assertInstanceOf(TestObject1::class, $result);
        $this->assertSame('1', $result->row1);
        $this->assertSame('2', $result->row2);
    }

    public function testQueryDBGetSingleClassThrowsExceptionWhenRecordDoesntExist(): void
    {
        $this->expectException(DbLibQueryException::class);
        $this->expectExceptionMessage('\PDOStatement::fetch returned false. Requested record does not exist.');

        $this->db->queryDBGetSingleResultAsClass(
            'SELECT * FROM test WHERE row1 = 16',
            TestObject1::class
        );
    }

    public function testQueryDBGetSingleResultAsClassThrowsExceptionWithMalformedSQL(): void
    {
        $this->expectException(DbLibQueryException::class);
        $this->expectExceptionMessage('PDO::query failed to execute statement. Check SQL syntax and/or class name.');

        /** @noinspection SyntaxError */
        $this->db->queryDBGetSingleResultAsClass(
            'SELECT * FROM test WHRE row1 = 1',
            TestObject1::class
        );
    }

    public function testQueryDBGetSingleResultAsClassThrowsExceptionWithMalformedClassName(): void
    {
        $this->pdo->exec('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2;');

        $this->expectException(DbLibQueryException::class);
        $this->expectExceptionMessage('PDO::query failed to execute statement. Check SQL syntax and/or class name.');

        /** @noinspection PhpUndefinedClassInspection */
        $this->db->queryDBGetSingleResultAsClass(
            'SELECT * FROM test WHERE row1 = 1',
            NonExistentClass::class
        );
    }

    public function testQueryGetAllResultsAsClassReturnsAnArrayOfObjects(): void
    {
        $this->pdo->exec('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2;');
        $this->pdo->exec('INSERT INTO dblibTest.test SET row1 = 2, row2 = 2;');

        $query = $this->db->queryDbGetAllResultsAsClass(
            'SELECT * FROM test;',
            TestObject1::class
        );

        $this->assertIsArray($query);
        $this->assertInstanceOf(TestObject1::class, $query[0]);
        $this->assertInstanceOf(TestObject1::class, $query[1]);

        $this->assertSame($query[0]->row1, '1');
        $this->assertSame($query[0]->row2, '2');
        $this->assertSame($query[1]->row1, '2');
        $this->assertSame($query[1]->row2, '2');
    }

    public function testQueryAllReturnsAnEmptyArrayIfNoResultsExist(): void
    {
        $query = $this->db->queryDbGetAllResultsAsClass(
            'SELECT * FROM test;',
            TestObject1::class
        );

        $this->assertSame([], $query);
    }
}
