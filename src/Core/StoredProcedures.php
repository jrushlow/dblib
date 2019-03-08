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
 * Date: 3/7/19 - 9:04 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Core;

/**
 * Class StoredProcedures
 *
 * @package Geeshoe\DbLib\Core
 */
class StoredProcedures extends AbstractConnection
{
    /**
     * Execute a Stored Procedure.
     *
     * Procedure & Params array should be properly escaped prior to using this
     * method.
     *
     * @param string $procedure
     * @param array  $params
     */
    public function executeStoredProcedure(string $procedure, array $params = []): void
    {
        $sql = 'CALL ' . $procedure . '();';

        if (!empty($params)) {
            $sql = preg_replace(
                '(\(\))',
                '("' . implode('", "', $params) . '")',
                $sql
            );
        }

        $this->connection->exec($sql);
    }
}
