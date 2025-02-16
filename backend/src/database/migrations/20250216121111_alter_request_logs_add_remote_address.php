<?php

class AlterRequestLogsAddRemoteAddress
{
    public function up(\PDO $db)
    {
        # define create table query
        $query = "ALTER TABLE request_logs ADD COLUMN remote_address VARCHAR(25);";
        # execute the query
        $db->exec($query);
    }

    public function down(\PDO $db)
    {
        # define delete query
        $query = "ALTER TABLE request_logs DROP COLUMN remote_address;";
        # execute the query
        $db->exec($query);
    }
}
