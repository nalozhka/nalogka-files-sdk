<?php

namespace Fostenslave\NalogkaFilesSDK\Exception;

use Fostenslave\NalogkaFilesSDK\Errors\AbstractError;

/**
 * Исключение, выбрасываемое в случае, когда API отдает ошибку.
 *
 * Исключение содержит в себе десереализованный объект ошибки
 *
 * @package Fostenslave\NalogkaFilesSDK\Exception
 */
class ApiErrorException extends NalogkaSdkException
{
    /**
     * @var AbstractError
     */
    private $error;

    public function __construct($error, $message = "", $code = 0)
    {
        parent::__construct($message, $code);

        $this->error = $error;
    }

    /**
     * @return AbstractError
     */
    public function getError()
    {
        return $this->error;
    }
}