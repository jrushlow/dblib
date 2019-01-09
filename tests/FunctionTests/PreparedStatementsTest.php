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
 * Date: 12/16/18 - 11:48 PM
 */

namespace Geeshoe\DbLibTests\FunctionTests;

use Geeshoe\DbLib\Core\PreparedStatements;
use Geeshoe\DbLib\Exceptions\DbLibException;
use Geeshoe\DbLib\Exceptions\DbLibQueryException;
use Geeshoe\DbLib\TestObject1;
use PHPUnit\Framework\TestCase;

/**
 * Class PreparedStatementsTest
 *
 * @package Geeshoe\DbLibTests\FunctionTests
 */
class PreparedStatementsTest extends TestCase
{
    /**
     * @var PreparedStatements
     */
    public $prepStmt;

    /**
     * @var \PDO
     */
    public $pdo;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $pdo = new \PDO('mysql:host=' . HOST . ';port=' . PORT, USER, PASS);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `dblibTest`');
        $pdo->exec('CREATE TABLE IF NOT EXISTS dblibTest.test(
                              row1 INT PRIMARY KEY,
                              row2 INT
                  )');

        $this->pdo = $pdo;

        $pdo->exec('USE dblibTest;');
        $this->prepStmt = new PreparedStatements($pdo);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->pdo->exec('DROP DATABASE `dblibTest`');
        $this->pdo = null;
    }

    public function testExecutePreparedInsertQuery(): void
    {
        $dataArray = ['row1' => '123', 'row2' => '321'];
        $this->prepStmt->executePreparedInsertQuery(
            'test',
            $dataArray
        );

        $result = $this->pdo->query('SELECT * FROM dblibTest.test');
        $result->execute();
        $result = $result->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertSame($dataArray, $result[0]);
    }

    public function testExecutePreparedInsertQueryThrowsExceptionOnFailure(): void
    {
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('Failed to execute prepared statement.');

        $this->prepStmt->executePreparedInsertQuery(
            'wrongTable',
            ['1' => '1']
        );
    }

    public function testPreparedFetchAsClassReturnClass(): void
    {
        $this->pdo->exec('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2;');
        $dataArray = ['row1' => '1'];
        $sql = 'SELECT * FROM test WHERE row1 = :row1';

        $result = $this->prepStmt->executePreparedFetchAsClass(
            $sql,
            $dataArray,
            TestObject1::class
        );

        $this->assertInstanceOf(TestObject1::class, $result);
    }

    public function testPreparedFetchAsClassThrowsExceptionIfFetchReturnsFalse(): void
    {
        $this->expectException(DbLibQueryException::class);
        $this->expectExceptionMessage('PDO::fetch() failed to retrieve a result.');

        $dataArray = ['row1' => '1'];
        $sql = 'SELECT * FROM test WHERE row1 = :row1';

        $this->prepStmt->executePreparedFetchAsClass(
            $sql,
            $dataArray,
            TestObject1::class
        );
    }

    public function testExecuteStmtThrowsExceptionWhenExecuteFails(): void
    {
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('Failed to execute prepared statement.');

        $dataArray = ['row1' => '1'];
        $sql = 'SELECT * FROM oops WHERE row1 = :row1';

        $this->prepStmt->executePreparedFetchAsClass(
            $sql,
            $dataArray,
            TestObject1::class
        );
    }
}
