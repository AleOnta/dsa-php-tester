<?php

namespace Backend\Controllers;

use Backend\Exceptions\InvalidApiKeyException;
use Backend\Exceptions\MissingApiKeyException;
use Backend\Models\ApiKey;

class ApiKeyController extends \Backend\Controllers\Controller
{

    private \Backend\Services\ApiKeyService $apiKeyService;
    private \Backend\Services\UserService $userService;

    public function __construct(\Backend\Services\ApiKeyService $apiKeyService, \Backend\Services\UserService $userService)
    {
        parent::__construct();
        $this->apiKeyService = $apiKeyService;
        $this->userService = $userService;
    }

    public function authenticate()
    {
        # extract request body
        $body = $this->request->body['content'];
        # check if user has included all required params
        $this->checkRequestBodyParameters(['username', 'password']);
        # check if the username exists
        $user_id = $this->userService->usernameExists($body['username']);
        # retrieve user hashed password
        $hash = $this->userService->getHashedPassword($user_id);
        # validate credentials
        if ($hash) {
            if (password_verify($body['password'], $hash)) {
                $apikey = $this->apiKeyService->fetchKey($user_id);
                $this->response(
                    201,
                    [
                        'status' => 'Success',
                        'message' => 'Successfully logged in',
                        'apikey' => $apikey->getApiKey(),
                        'expiresIn' => $apikey->secondsToExipration(),
                        'expirationTime' => $apikey->getExpiresAt()
                    ]
                );
            } else {
                $this->response(
                    401,
                    [
                        'status' => 'Unauthorized',
                        'message' => 'Incorrect credentials'
                    ]
                );
            }
        }
    }

    public function refresh()
    {
        # extract the apikey from the headers
        $apikey = $this->request->getApiKey();
        # check if apikey is set
        if (empty($apikey)) {
            throw new MissingApiKeyException();
        }
        # check if the api key exists and is associated with a user
        $apikeyId = $this->apiKeyService->keyExists($apikey);
        if (!$apikeyId) {
            throw new InvalidApiKeyException(substr($apikey, -6));
        }
        # generate a new apikey
        $newApiKey = \Backend\Models\ApiKey::generateApiKey();
        # update the current entity
        $this->apiKeyService->updateApiKey($apikeyId['id'], $newApiKey);
        # retrieve the updated key
        $apikey = (new ApiKey())->hydrate($this->apiKeyService->fetchKeyById($apikeyId['id']));
        # return new apikey to the client
        $this->response(
            200,
            [
                'status' => 'Success',
                'apikey' => $apikey->getApiKey(),
                'expiresIn' => $apikey->secondsToExipration(),
                'expirationTime' => $apikey->getExpiresAt()
            ]
        );
    }
}
