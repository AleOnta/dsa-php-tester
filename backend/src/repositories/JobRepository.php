<?php

namespace Backend\Repositories;

use Backend\Models\Job;
use PDO;

class JobRepository extends Repository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'async_jobs');
    }

    public function findByJobId(int $id)
    {
        return $this->findById($id);
    }

    public function create(Job $job)
    {
        # define the query
        $query = "INSERT INTO {$this->table} (file, status, progress, message, created_at, updated_at, dataset_id) ";
        $query .= "VALUES (:file, :status, :progress, :message, :created_at, :updated_at, :dataset_id);";
        # prepare the statement
        $stmt = $this->db->prepare($query);
        # execute the query
        $res = $stmt->execute($job->values());
        # return job id or false
        if ($res) {
            return $this->db->lastInsertId();
        }
        return $res;
    }

    public function update(int $id, array $data)
    {
        # adds the dataset id
        $data['id'] = $id;
        # adds the updated at value as default
        $data['updated_at'] = date('Y-m-d H:i:s');
        # create to update statement
        $update = "";
        foreach ($data as $col => $val) {
            $update .= "{$col} = :{$col}, ";
        }
        # remove last comma and white space
        $update = substr($update, 0, (strlen($update) - 2));
        # define the query
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$update} WHERE id = :id;");
        # execute the query
        return $stmt->execute($data);
    }
}
