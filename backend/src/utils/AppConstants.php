<?php

namespace Backend\Utils;

class AppConstants
{
    # define the root path of the application
    public const ROOT_DIR = __DIR__ . "/../../";

    # define child paths
    public const ENV_PATH = self::ROOT_DIR . ".env";
    public const UTILS_DIR = self::ROOT_DIR . "src/utils/";
    public const CONFIG_DIR = self::ROOT_DIR . "config/";
    public const ROUTES_DIR = self::ROOT_DIR . "src/routes/";
    public const UPLOADS_DIR = self::ROOT_DIR . "src/uploads/";
    public const DATASETS_DIR = self::ROOT_DIR . "src/uploads/datasets/";
    public const MIGRATIONS_DIR = self::ROOT_DIR . "src/database/migrations/";
}
