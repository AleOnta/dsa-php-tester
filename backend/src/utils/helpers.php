<?php

function dd($var = 'no var passed'): void
{
    var_dump($var);
    die();
}

function fatal_error_shutdown(): void {}
