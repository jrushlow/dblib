<?php
/**
 * Copyright 2019 Jesse Rushlow - Geeshoe Development
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
 * Date: 4/2/19 - 7:26 PM
 */

namespace Geeshoe\DbLibTests\FunctionTests;

use Geeshoe\DbLib\Core\PreparedStoredProcedures;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;
use PDO;
use PDOException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class PreparedStoredProceduresTest
 *
 * @package Geeshoe\DbLibTests\FunctionTests
 */
class PreparedStoredProceduresTest extends TestCase
{
    /**
     * @var PreparedStoredProcedures
     */
    public $prepStmt;

    /**
     * @var PDO
     */
    public $pdo;

    /**
     * @inheritdoc
     * @throws PDOException
     */
    public function setUp()
    {
        $pdo = new PDO('mysql:host=' . HOST . ';port=' . PORT, USER, PASS);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `dblibTest`');
        $pdo->exec('CREATE TABLE IF NOT EXISTS dblibTest.test(
                              row1 INT PRIMARY KEY,
                              row2 INT
                  )');

        $this->pdo = $pdo;

        $pdo->exec('USE dblibTest;');
        $this->prepStmt = new PreparedStoredProcedures($pdo);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->pdo->exec('DROP DATABASE `dblibTest`');
        $this->pdo = null;
    }

    /**
     * @throws DbLibPreparedStmtException
     * @throws PDOException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testExecutePreparedStoredProcedure(): void
    {
        $procedure = 'CREATE PROCEDURE getAllRows(item INT) BEGIN INSERT INTO test SET row1 = item, row2 = 30; END;';
        $this->pdo->exec($procedure);

        $this->prepStmt->executePreparedStoredProcedure('getAllRows', ['row1' => 20]);

        $query = $this->pdo->query('SELECT * FROM test WHERE row1 = 20;');
        $query->execute();
        $result = $query->fetch();

        $this->assertSame('20', $result['row1']);
    }
}
