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
 * Date: 12/16/18 - 11:01 PM
 */

namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Core\PreparedStatements;
use Geeshoe\DbLib\Exceptions\DbLibException;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PreparedStatementsTest
 *
 * @package Geeshoe\DbLibTests
 */
class PreparedStatementsTest extends TestCase
{
    /**
     * @var MockObject`
     */
    public $pdo;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecutePreparedInsertQueryThrowsExceptionWithEmptyDataArray(): void
    {
        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('$userSuppliedData array must not be empty.');

        $stmt = new PreparedStatements($this->pdo);
        $stmt->executePreparedInsertQuery('someTable', []);
    }

    public function testPrepareStatementThrowsExceptionOnFalse(): void
    {
        $this->pdo->method('prepare')
            ->willReturn(false);

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('Database is unable to prepare statement.');

        $statement = new PreparedStatements($this->pdo);

        $statement->executePreparedInsertQuery(
            'someTable',
            ['someColumn' => 'someValue']
        );
    }

    public function testPrepareStatementCatchesPDOExceptionThrownOnFailure(): void
    {
        $this->pdo->method('prepare')
            ->willThrowException(new PDOException());

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('Database is unable to prepare statement.');

        $stmt = new PreparedStatements($this->pdo);
        $stmt->executePreparedInsertQuery(
            'someTable',
            ['someColumn' => 'someValue']
        );
    }

    public function testBindValueThrowsException(): void
    {
        $this->pdo->method('prepare')
            ->willReturn(new PDOStatement());

        $prepare = new PreparedStatements($this->pdo);

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage(
            'Failed to bind value using Placeholder: (:someColumn) Value: (someValue).'
        );
        $prepare->executePreparedInsertQuery(
            'someTable',
            ['someColumn' => 'someValue']
        );
    }

    public function testExecuteStmtThrowsExceptionOnFailure(): void
    {
        $this->expectException(DbLibPreparedStmtException::class);
        $this->expectExceptionMessage('Failed to execute prepared statement.');

        $class = new class($this->pdo) extends PreparedStatements
        {
            public function __construct()
            {
            }

            public function check(PDOStatement $stmt): void
            {
                $this->executeStmt($stmt);
            }
        };

        $stmt = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stmt->method('execute')
            ->willThrowException(new PDOException('Some failure.'));

        $class->check($stmt);
    }
}
