<?php

namespace Backend\Controllers;

use Backend\Services\UserService;

class UserController extends Controller {

    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function register() {
        $this->response(200, ['status' => true, 'message' => 'registering user']);
    }
}