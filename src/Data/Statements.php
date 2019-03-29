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
 * Date: 12/15/18 - 1:37 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Data;

/**
 * Class Statements
 *
 * @package Geeshoe\DbLib\Core
 */
class Statements
{
    /**
     * @param array $dataArray
     * @return array
     */
    public static function parseDataArray(array $dataArray): array
    {
        foreach ($dataArray as $column => $value) {
            $sqlColumnPlaceHolderPair[] = $column . ' = :' . $column;
            $values[':' . $column] = $value;
        }

        return ['placeHolderArray' => $sqlColumnPlaceHolderPair, 'values' => $values];
    }

    /**
     * @param array $dataArray
     *
     * @return array
     */
    public static function parseDataArrayForStoredProcedure(array $dataArray): array
    {
        foreach ($dataArray as $column => $value) {
            $sqlColumnPlaceHolderPair[] =  ':' . $column;
            $values[':' . $column] = $value;
        }

        return ['placeHolderArray' => $sqlColumnPlaceHolderPair, 'values' => $values];
    }

    /**
     * Create an SQL INSERT statement & data array.
     *
     * This method is automatically called by
     * PreparedStatements::executePreparedInsertQuery when executing a prepared
     * statement. It may however be used independently of the DbLib library.
     *
     * @param string $table             Table name used by SQL Database.
     * @param array  $userSuppliedData ['MySQL_Column' => 'Sanitized_Value']
     * @return array                    ['sql' => 'SQL_Statement, [':MySQL_Column' => 'Value']
     */
    public static function prepareInsertQueryData(string $table, array $userSuppliedData): array
    {
        $dataArray = self::parseDataArray($userSuppliedData);

        $sql = 'INSERT INTO '.$table.' SET '. implode(', ', $dataArray['placeHolderArray']);

        return ['sql' => $sql, 'values' => $dataArray['values']];
    }

    /**
     * @param array $userSuppliedData
     * @return array
     */
    public static function getValuesArray(array $userSuppliedData): array
    {
        $values = self::parseDataArray($userSuppliedData);

        return $values['values'];
    }
}
