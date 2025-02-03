<?php

class CreateDatasetsTable
{

    public function up(\PDO $db)
    {
        # define create table query
        $query = "CREATE TABLE datasets (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            type VARCHAR(255) NOT NULL,
            size INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            uploaded BOOLEAN NOT NULL DEFAULT FALSE
        )";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # define delete query
        $query = "DROP TABLE datasets;";
        # execute the query
        $db->exec($query);
    }
}
