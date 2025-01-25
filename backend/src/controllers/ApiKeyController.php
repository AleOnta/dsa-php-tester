<?php

namespace Backend\Controllers;

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
                $apiKey = $this->apiKeyService->fetchKey($user_id);
                $this->response(
                    200,
                    [
                        'status' => 'Success',
                        'message' => 'Successfully logged in',
                        'apikey' => $apiKey->getApiKey(),
                        'expiresIn' => $apiKey->secondsToExipration(),
                        'expirationTime' => $apiKey->getExpiresAt()
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
        # extract request body

    }
}
