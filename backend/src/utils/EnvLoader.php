<?php

namespace Backend\Utils;

use Exception;
use Backend\Utils\AppConstants;

class EnvLoader
{

    public function loadEnv($envPath = AppConstants::ENV_PATH)
    {

        # check file existence
        if (!file_exists($envPath)) {
            throw new Exception("ENV file not found in {$envPath}");
        }

        # read the file
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        # loop through the file lines
        foreach ($lines as $line) {

            # check commented lines
            if (strpos($line, '#') !== FALSE) continue;

            # get the key value pair
            [$key, $val] = explode('=', $line, 2);

            # set the variable
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}
