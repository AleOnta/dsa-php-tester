<?php

function dd($var = 'no var passed'): void
{
    var_dump($var);
    die();
}

function fatal_error_shutdown(): void
{
    $lastError = error_get_last();
    if (isset($lastError)) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Something went wrong',
            'last_error' => $lastError
        ]);
        die();
    }
}
