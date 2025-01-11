<?php

namespace Backend\Middlewares;

class Middleware {

    # protected method to return a early response to the client
    protected function return(int $code, bool $status, array $payload) {
        http_response_code($code);
        echo json_encode(['status' => $status, 'data' => $payload]);
        exit;
    }

    # protected method to redirect client
    protected function redirect(string $location, int $code=302) {
        http_response_code($code);
        header("Location: {$location}");
        exit;
    }

}