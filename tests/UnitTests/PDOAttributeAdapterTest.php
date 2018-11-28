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
 * Date: 11/27/18 - 9:14 PM
 */

namespace Geeshoe\DbLibTests;

use Geeshoe\DbLib\Config\PDOAttributeAdapter;
use Geeshoe\DbLib\Exceptions\DbLibException;
use PHPUnit\Framework\TestCase;

/**
 * Class PDOAttributeAdapterTest
 *
 * @package Geeshoe\DbLibTests
 */
class PDOAttributeAdapterTest extends TestCase
{
    /**
     * @var \PDO|null
     */
    public $pdo = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Assert exception DbLibException is thrown with invalid PDO Attribute.
     */
    public function testSetAttributeThrowsExceptionWithInvalidAttribute()
    {
        $test = new PDOAttributeAdapter(
            $this->pdo,
            ['PDO::AkTTR_CASE' => 'PDO::CASE_LOWER', 'PDO::ATTR_CASE' => 'PDO::CASE_LOWER']
        );

        $this->expectException(DbLibException::class);
        $this->expectExceptionMessage('PDO Attributes PDO::AkTTR_CASE - PDO::CASE_LOWER are invalid.');
        $test->setAttribute();
    }

    /**
     * Assert a valid PDO instance is returned with valid attributes supplied.
     */
    public function testPDOInstanceReturnedBySetAttributesWhenValidAttributesAreSupplied()
    {
        $test = new PDOAttributeAdapter(
            $this->pdo,
            ['PDO::ATTR_CASE' => 'PDO::CASE_LOWER', 'PDO::ATTR_ERRMODE' => 'PDO::ERRMODE_WARNING']
        );

        $this->assertInstanceOf(\PDO::class, $test->setAttribute());
    }
}
