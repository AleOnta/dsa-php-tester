<?php

namespace Backend\Database;

use PDO;

class Database {

    protected static ?PDO $instance;
    
    public static function getInstance(): PDO {

        # return existing singleton
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        # load env into global variable
        $envLoader = new \Backend\Utils\EnvLoader();
        $envLoader->loadEnv();

        # attempt database connection
        try {
            
            # compose dsn
            $dsn = "pgsql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};";
            # connect to the database
            self::$instance = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PSW']);
            # set error mode in PDO instance
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            echo PHP_EOL."connection to the database has failed...";
            echo PHP_EOL."error message: ".$e->getMessage();
            die();
        }
        # return the singleton
        return self::$instance;
    }

    public static function setCharsetEncoding(): void {

        # check for existing db connection
        if (!empty(self::$instance)) {
            self::getInstance();
        }

        # set client encoding
        self::$instance->exec(
            "SET NAMES 'UTF8';
            SET client_encoding  = 'UTF8';"
        );
    }

}