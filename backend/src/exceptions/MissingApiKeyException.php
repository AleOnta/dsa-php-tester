<?php

namespace Backend\Exceptions;

class MissingApiKeyException extends \Exception
{
    public function __construct()
    {
        parent::__construct('This endpoint requires the usage of an apikey for authentication. Please provide a valid apikey');
    }
}
