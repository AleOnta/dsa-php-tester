<?php

class CreateAsyncJobsTable
{

    public function up(\PDO $db)
    {
        # define create table query
        $query = "CREATE TABLE async_jobs (
            id SERIAL PRIMARY KEY,
            file TEXT,
            status TEXT NOT NULL,
            PROGRESS int DEFAULT 0,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # define delete query
        $query = "DROP TABLE async_jobs;";
        $db->exec($query);
    }
}
