<?php

class CreateJsonDatasetsContent
{

    public function up(\PDO $db)
    {
        # define create table query
        $query = "CREATE TABLE json_datasets_content (
            id SERIAL PRIMARY KEY,
            dataset_id INT NOT NULL CONSTRAINT fk_jsoncontent_datasets REFERENCES datasets (id) ON UPDATE CASCADE ON DELETE CASCADE,
            object JSONB NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # drop the constraint
        $query = "ALTER TABLE json_datasets_content DROP CONSTRAINT fk_jsoncontent_datasets;";
        # execute the quer
        $db->exec($query);
        # define delete query
        $query = "DROP TABLE json_datasets_content;";
        # execute the query
        $db->exec($query);
    }
}
