<?php

namespace Backend\Exceptions;

class AuthenticationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors = [])
    {
        parent::__construct('Authentication has failed.');
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
