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
 * Date: 4/2/19 - 10:10 AM
 */
declare(strict_types=1);

namespace Geeshoe\DbLib\Exceptions;

use Throwable;

/**
 * Class DbLibPreparedStmtException
 *
 * @package Geeshoe\DbLib\Exceptions
 */
class DbLibPreparedStmtException extends DbLibException
{
    /**
     * DbLibPreparedStmtException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    protected static function throwException(string $message, int $code, Throwable $previous = null): void
    {
        throw new DbLibPreparedStmtException(
            $message,
            $code,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function dataArrayInvalid(Throwable $previous = null): void
    {
        self::throwException(
            '$userSuppliedData array must not be empty.',
            0,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function executeStmtFailed(Throwable $previous = null): void
    {
        self::throwException(
            'Failed to execute prepared statement.',
            0,
            $previous
        );
    }

    /**
     * @param string    $placeHolder
     * @param string    $value
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function bindValueFailed(string $placeHolder, string $value, Throwable $previous = null): void
    {
        self::throwException(
            'Failed to bind value using Placeholder: (' . $placeHolder . ') Value: (' . $value . ').',
            0,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function fetchFailed(Throwable $previous = null): void
    {
        self::throwException(
            'PDO::fetch() failed to retrieve a result.',
            0,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function prepareFailed(Throwable $previous = null): void
    {
        self::throwException(
            'Database is unable to prepare statement.',
            0,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function preparedInsertQueryFailed(Throwable $previous = null): void
    {
        self::throwException(
            'Failed to execute the prepared insert query.',
            0,
            $previous
        );
    }

    /**
     * @param Throwable|null $previous
     *
     * @throws DbLibPreparedStmtException
     */
    public static function preparedNoParamsFailed(Throwable $previous = null): void
    {
        self::throwException(
            'Failed to execute prepared statement with no params.',
            0,
            $previous
        );
    }
}
