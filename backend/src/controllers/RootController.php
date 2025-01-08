<?php

namespace Backend\Controllers;

class RootController extends Controller
{
    public function index()
    {
        echo "<h1>Root Response</h1>";
        $this->response(200, ['message' => 'Root Controller Response']);
    }
}
