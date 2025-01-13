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
        dd($this->request);
    }
}
