<?php

namespace Backend\Repositories;

use PDO;

class DatasetsRepository extends Repository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'datasets');
    }

    public function findDatasetById(int $id)
    {
        # define the query
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id;");
        # bind id param
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        # execute the query
        $stmt->execute();
        # return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? false;
    }

    public function create(string $name, string $type, int $size)
    {
        # define the query
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, type, size) VALUES (?, ?, ?);");
        # execute the query
        $insert = $stmt->execute([$name, $type, $size]);
        # check the operation result
        if ($insert) {
            # return dataset id
            return $this->db->lastInsertId();
        }
        return false;
    }
}
