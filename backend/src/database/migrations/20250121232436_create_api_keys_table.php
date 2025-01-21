<?php

class CreateApiKeysTable
{

    public function up(\PDO $db)
    {
        # define the query
        $query = "CREATE TABLE api_keys (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            api_key VARCHAR(60) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        );";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # define the query
        $query = "DROP TABLE api_keys;";
        # execute the query
        $db->exec($query);
    }
}
