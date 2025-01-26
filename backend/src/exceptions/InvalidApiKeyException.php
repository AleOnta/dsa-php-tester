<?php

namespace Backend\Exceptions;

class InvalidApiKeyException extends \Exception
{
    public function __construct(string $apikey)
    {
        parent::__construct("ApiKey ending with '...{$apikey}' is invalid. Authenticate at /api/v1/auth/login to get a new apikey");
    }
}
