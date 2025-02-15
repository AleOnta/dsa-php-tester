<?php

namespace Backend\Controllers;

use Backend\Services\UserService;
use Backend\Exceptions\ValidationException;
use Backend\Exceptions\InvalidRequestException;
use Backend\Exceptions\MissingParameterException;

class UserController extends Controller
{

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    public function register()
    {
        # extract data from request body
        $body = $this->request->body['content'];
        # check for missing parameters in req body
        $missing = $this->checkRequestBodyParameters(['username', 'email', 'password']);
        # validate the username input
        $username = $this->userService->validateUsername($body['username']);
        # validate the email input
        $email = $this->userService->validateEmail($body['email']);
        # validate the password input
        $password = $this->userService->validatePassword($body['password']);
        # create the user
        $userId = $this->userService->create($username, $email, $password);
        # return response
        $this->response(
            202,
            [
                'status' => 'Ok',
                'message' => 'User created correctly, authenticate to receive your api key',
                'id' => $userId
            ]
        );
    }

    public function update(int $id)
    {
        # retrieve the user by id
        $userId = $this->userService->validateUserId($id);
        # extract the request body
        $body = $this->request->body['content'];
        # check for available parameters in the body
        $this->checkPossibleRequestBodyParameters(['username', 'email', 'new_password']);
        # check if the user authentication (password) is present
        if (!isset($body['current_password']) || trim($body['current_password']) === '') {
            throw new MissingParameterException(
                'Authentication Error',
                ['current_password' => 'Your current password is required for this operation.']
            );
        }
        # check if the forwarded password is valid
        $this->userService->authenticateWithPassword($id, $body['current_password']);
        $updates = ['query' => [], 'values' => []];
        # check username value
        if (isset($body['username'])) {
            $username = $this->userService->validateUsername($body['username']);
            $updates['query'][] = 'username = :username';
            $updates['values'][] = $body['username'];
        }
        # check the email value
        if (isset($body['email'])) {
            $email = $this->userService->validateEmail($body['email']);
            $updates['query'][] = 'email = :email';
            $updates['values'][] = $body['email'];
        }
        # check the password field
        if (isset($body['new_password'])) {
            $new_password = $this->userService->validatePassword($body['new_password']);
            $updates['query'][] = 'password = :password';
            $updates['values'][] = password_hash($body['new_password'], PASSWORD_BCRYPT);
        }
        # append the user id to the values
        $updates['values'][] = $id;
        # update the user data
        $this->userService->update($updates);
        # return response to the client
        $this->response(
            200,
            [
                'status' => 'Ok',
                'message' => 'User updated successfully',
            ]
        );
    }
}
