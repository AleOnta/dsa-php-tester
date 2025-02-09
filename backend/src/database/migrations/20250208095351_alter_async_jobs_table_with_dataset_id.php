<?php

class AlterAsyncJobsTableWithDatasetId
{

    public function up(\PDO $db)
    {
        # define create table query
        $query = "
        ALTER TABLE async_jobs ADD COLUMN dataset_id INT NOT NULL
        CONSTRAINT fk_datasets_jobs REFERENCES datasets (id)
        ON UPDATE CASCADE ON DELETE CASCADE
        ";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # $drop the contraint on the column
        $query = "ALTER TABLE async_jobs DROP CONSTRAINT fk_datasets_jobs;";
        # execute the query
        $db->exec($query);
        # drop the column
        $query = "ALTER TABLE async_jobs DROP COLUMN dataset_id;";
        # execute the query
        $db->exec($query);
    }
}
