<?php

class CreateUserTable {

    public function up(PDO $db) {
        $query = "
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->exec($query);
    }

    public function down(PDO $db) {
        $query = "DROP TABLE IF EXISTS users;";
        $db->exec($query);
    }

}