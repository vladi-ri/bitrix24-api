<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Exception;

/**
 * Class Bitrix24APIException
 * Exception handler in Bitrix24API class
 *
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api-php
 * @license   OpenSource
 *
 * @version 1.0.0
 *
 * v1.0.0 (16.02.2024) Introduce Bitrix24API PHP project
 */
class Bitrix24APIException extends Exception
{
    /**
     * Adds an identification string to the exception message
     *
     * @param string         $message  Deletion message
     * @param int            $code     Exception code
     * @param Exception|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct("Bitrix24API: " . $message, $code, $previous);
    }
}
