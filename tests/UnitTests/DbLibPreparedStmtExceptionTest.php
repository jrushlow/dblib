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
 * Date: 4/2/19 - 7:45 PM
 */

namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;
use PHPUnit\Framework\TestCase;

/**
 * Class DbLibPreparedStmtExceptionTest
 *
 * @package Geeshoe\DbLibTests
 */
class DbLibPreparedStmtExceptionTest extends TestCase
{
    /**
     * @return array
     */
    public function exceptionDataProvider(): array
    {
        return [
            'Data array invalid' => [
                'dataArrayInvalid',
                '$userSuppliedData array must not be empty',
                0
            ],
            'Exec Stmt Failed' => [
                'executeStmtFailed',
                'Failed to execute prepared statement.',
                0
            ],
            'Fetch Failed' => [
                'fetchFailed',
                'PDO::fetch() failed to retrieve a result.',
                0
            ],
            'Prepare Failed' => [
                'prepareFailed',
                'Database is unable to prepare statement.',
                0
            ],
            'Prepared Insert Query Failed' => [
                'preparedInsertQueryFailed',
                'Failed to execute the prepared insert query.',
                0
            ],
            'Prepared No Params Failed' => [
                'preparedNoParamsFailed',
                'Failed to execute prepared statement with no params.',
                0
            ]
        ];
    }

    /** @noinspection PhpDocRedundantThrowsInspection */
    /**
     * @dataProvider exceptionDataProvider
     *
     * @param string $method
     * @param string $message
     * @param int    $code
     *
     * @throws DbLibPreparedStmtException
     */
    public function testExceptionThrown(string $method, string $message, int $code): void
    {
        $this->expectException(DbLibPreparedStmtException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);

        DbLibPreparedStmtException::$method();
    }
}
