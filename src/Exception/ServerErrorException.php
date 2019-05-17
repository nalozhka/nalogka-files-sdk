<?php


namespace Fostenslave\NalogkaFilesSDK\Exception;

/**
 * Исключение, выбрасываемое в случае неудачного запроса к серверу
 *
 * @package Fostenslave\NalogkaFilesSDK\Exception
 */
class ServerErrorException extends NalogkaSdkException
{
    public function __construct($code, $message = "", \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}