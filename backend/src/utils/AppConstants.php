<?php

namespace Backend\Utils;

class AppConstants
{
    # define the root path of the application
    public const ROOT_DIR = dirname(dirname(__DIR__));
    public const ENV_PATH = self::ROOT_DIR . ".env";
    public const CONFIG_DIR = self::ROOT_DIR . "config/";
    public const UTILS_DIR = self::ROOT_DIR . "src/utils/";
}
