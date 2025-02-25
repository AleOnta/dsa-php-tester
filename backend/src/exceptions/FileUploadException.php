<?php

namespace Backend\Exceptions;

class FileUploadException extends \Exception
{
    private array $errors;

    public function __construct($message, $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
