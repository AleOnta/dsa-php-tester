<?php

use Backend\Core\Container;

function dd($var = 'no var passed'): void
{
    var_dump($var);
    die();
}

function rateLimitSetting(Container $c, string $endpoint, int $requests = 2, int $seconds = 3600)
{
    return [
        'db' => $c->get('db'),
        'endpoint' => $endpoint,
        'limit' => $requests,
        'window' => $seconds
    ];
}

function fatal_error_shutdown(): void {}
