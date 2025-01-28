<?php

namespace Backend\Middlewares;

use Backend\Exceptions\ExpiredApiKeyException;
use Backend\Exceptions\InvalidApiKeyException;
use Backend\Exceptions\MissingApiKeyException;
use Backend\Services\ApiKeyService;

class AuthMiddleware extends Middleware
{

    private array $requiredRoles;
    private ApiKeyService $apikeyService;

    public function __construct(ApiKeyService $apikeyService, array $requiredRoles = [])
    {
        $this->requiredRoles = $requiredRoles;
        $this->apikeyService = $apikeyService;
    }

    public function handle($request, \Closure $next)
    {
        # extract apikey if present
        $apikey = $request['HTTP_AUTHORIZATION'] ?? false;
        if (!$apikey) {
            # block requests without apikey
            throw new MissingApiKeyException();
        }
        # remove Bearer ...
        $apikey = str_replace('Bearer ', '', $apikey);
        # check if the apikey is exists
        $apikeyId = $this->apikeyService->keyExists($apikey);
        if (!isset($apikeyId['id'])) {
            # block request with invalid apikey
            throw new InvalidApiKeyException($apikey);
        }
        # retrieve api key
        $apikeyEntity = $this->apikeyService->fetchKeyById($apikeyId['id']);
        # check apikey validity by expiration
        if (!$apikeyEntity->isValid()) {
            throw new ExpiredApiKeyException();
        }
        # allow the request
        $next();
    }
}
