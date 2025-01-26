<?php

class CreateRequestLogsTable
{
    public function up(\PDO $db)
    {
        # define create table query
        $query = "CREATE TABLE request_logs (
            id SERIAL PRIMARY KEY,
            api_key VARCHAR(64),
            endpoint VARCHAR(255) NOT NULL,
            request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX idx_api_key_time ON request_logs (api_key, request_time);";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # define delete query
        $query = "DROP TABLE request_logs;";
        # execute the query
        $db->exec($query);
    }
}
