<?php

namespace Backend\Controllers;

use Backend\Services\UserService;

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
        # if any required parameter is missing, return errors
        if (count($missing) > 0) {
            throw new \Backend\Exceptions\MissingParameterException('Missing parameters', $missing);
        }
        # validate the username input
        $username = $this->userService->validateUsername($body['username']);
        if (is_array($username)) {
            throw new \Backend\Exceptions\ValidationException('Username is invalid', ['username' => $username[1]]);
        }
        # check if the username has already been registered
        if ($this->userService->usernameExists($username)) {
            # return response to the client without sharing the username existence
            $error = 'Username cannot be processed, try to change its value with an alphanumeric string with a length between 6-16 chars';
            throw new \Backend\Exceptions\InvalidRequestException('Bad Request', ['username' => $error]);
        }
        # validate the email input
        $email = $this->userService->validateEmail($body['email']);
        if (is_array($email)) {
            throw new \Backend\Exceptions\ValidationException('Email is invalid', ['email' => $email[1]]);
        }
        # check if the email has already been registered
        if ($this->userService->usernameExists($username)) {
            # return response to the client without sharing the username existence
            $error = 'Email address cannot be processed...';
            throw new \Backend\Exceptions\InvalidRequestException('Bad Request', ['email' => $error]);
        }
        # validate the password input
        $password = $this->userService->validatePassword($body['password']);
        if (is_array($password)) {
            throw new \Backend\Exceptions\ValidationException('Password is invalid', ['password' => $password[1]]);
        }
        # create the user
        $userId = $this->userService->create($username, $email, $password);
        # return response
        $this->response(202, ['status' => 'Ok', 'message' => 'User created correctly', 'id' => $userId]);
    }
}
