<?php

namespace Backend\Exceptions;

class InvalidRequestException extends \Exception
{
    private array $errors;

    public function __construct(string $message, $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
