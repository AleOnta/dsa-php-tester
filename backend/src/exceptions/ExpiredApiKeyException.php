<?php

namespace Backend\Exceptions;

class ExpiredApiKeyException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Your apikey is expired. Generate a new apikey at /api/v1/auth/refresh-apikey or authenticate at /api/v1/auth/login');
    }
}
