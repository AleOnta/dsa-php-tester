<?php

namespace Backend\Controllers;

class RootController extends Controller
{
    public function index()
    {
        $this->response(200, ['message' => 'Root Controller Response']);
    }
}
