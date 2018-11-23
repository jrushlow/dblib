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
declare(strict_types=1);

namespace Geeshoe\DbLibTests\FunctionTests;

use Geeshoe\DbLib\Core\DbLib;
use PHPUnit\Framework\TestCase;

class DbLibFuncTest extends TestCase
{

    /**
     * @var DbLib
     */
    public $db;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $pdo = new \PDO('mysql:host=' . HOST . ';port=' . PORT, USER, PASS);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `dblibTest`');
        $pdo->exec('CREATE TABLE IF NOT EXISTS dblibTest.test(
                              row1 INT PRIMARY KEY,
                              row2 INT
                  )');

        $pdo = null;

        $this->db = new DbLib(__DIR__ . '/credentials/dblibConfig.json');
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $pdo = new \PDO('mysql:host=' . HOST . ';port=' . PORT, USER, PASS);

        $pdo->exec('DROP DATABASE `dblibTest`');
        $pdo = null;
    }

    public function testExecuteWithNoReturnAndSingleReturn()
    {

        $this->db->executeQueryWithNoReturn('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2');

        $query = $this->db->executeQueryWithSingleReturn('SELECT * FROM dblibTest.test', \PDO::FETCH_ASSOC);

        $expected = array(
            'row1' => 1,
            'row2' => 2
        );

        self::assertEquals($expected, $query);
    }

    public function testExecuteWithAllReturned()
    {
        $this->db->executeQueryWithNoReturn('INSERT INTO dblibTest.test SET row1 = 1, row2 = 2');
        $this->db->executeQueryWithNoReturn('INSERT INTO dblibTest.test SET row1 = 6, row2 = 5');

        $query = $this->db->executeQueryWithAllReturned('SELECT * FROM dblibTest.test', \PDO::FETCH_ASSOC);

        $expected = array(
            0 => [
                'row1' => 1,
                'row2' => 2
            ],
            1 => [
                'row1' => 6,
                'row2' => 5
            ]

        );

        self::assertEquals($expected, $query);
    }

    public function testManipulateDataWithNoAndSingleReturn()
    {
        $sql = 'INSERT INTO dblibTest.test (row1, row2) VALUES (:a, :b)';
        $array = array(
            ':a' => 10,
            ':b' => 20
        );

        $this->db->manipulateDataWithNoReturn($sql, $array);

        $qSql = 'SELECT * FROM dblibTest.test WHERE row1 = :num';

        $qArray = array(
            ':num' => 10
        );

        $query = $this->db->manipulateDataWithSingleReturn($qSql, $qArray, \PDO::FETCH_ASSOC);

        $expected = array(
            'row1' => 10,
            'row2' => 20
        );

        self::assertEquals($expected, $query);
    }

    public function testManipulateDataWithAllReturn()
    {
        $sql = 'INSERT INTO dblibTest.test (row1, row2) VALUES (:a, :b)';
        $arrayA = array(
            ':a' => 10,
            ':b' => 20
        );
        $arrayB = array(
            ':a' => 11,
            ':b' => 20
        );

        $this->db->manipulateDataWithNoReturn($sql, $arrayA);
        $this->db->manipulateDataWithNoReturn($sql, $arrayB);

        $qSql = 'SELECT * FROM dblibTest.test WHERE row2 = :num';

        $qArray = array(
            ':num' => 20
        );

        $query = $this->db->manipulateDataWithAllReturned($qSql, $qArray, \PDO::FETCH_ASSOC);

        $expected = array(
            0 => [
                'row1' => 10,
                'row2' => 20
                ],
            1 => [
                'row1' => 11,
                'row2' => 20
            ],
        );

        self::assertEquals($expected, $query);
    }
}
