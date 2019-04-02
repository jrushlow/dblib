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
 * Date: 3/29/19 - 2:58 PM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Core;

use Geeshoe\DbLib\Data\Statements;
use Geeshoe\DbLib\Exceptions\DbLibPreparedStmtException;

/**
 * Class PreparedStoredProcedures
 *
 * @package Geeshoe\DbLib\Core
 */
class PreparedStoredProcedures extends PreparedStatements
{
    /**
     * @param string $procedure
     * @param array  $params
     *
     * @throws DbLibPreparedStmtException
     */
    public function executePreparedStoredProcedure(string $procedure, array $params): void
    {
        $dataArray = Statements::parseDataArrayForStoredProcedure($params);

        $sql = 'CALL '.$procedure.'('.implode(', ', $dataArray['placeHolderArray']).');';

        $stmt = $this->prepareStatement($sql);

        foreach ($dataArray['values'] as $placeHolder => $value) {
            $this->bindValue($stmt, $placeHolder, $value);
        }

        $this->executeStmt($stmt);
    }
}
