<?php


namespace Fostenslave\NalogkaFilesSDK\Errors;


class ValidationError extends AbstractError
{
    /**
     * @var ItemValidationError[]
     */
    public $errors;
}