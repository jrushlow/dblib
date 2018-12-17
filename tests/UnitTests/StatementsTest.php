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
 * Date: 12/15/18 - 1:43 PM
 */

namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Data\Statements;
use PHPUnit\Framework\TestCase;

class StatementsTest extends TestCase
{
    public function testPrepareInsertQueryDataReturnsValidArray(): void
    {
        $data = Statements::prepareInsertQueryData(
            'SomeTable',
            ['C1' => 'data1', 'C2' => 'data2']
        );

        $expectedSql = 'INSERT INTO SomeTable SET C1 = :C1, C2 = :C2';
        $expectedValuesArray = [
            ':C1' => 'data1',
            ':C2' => 'data2'
        ];

        $this->assertSame($expectedSql, $data['sql']);
        $this->assertSame($expectedValuesArray, $data['values']);
    }
}
