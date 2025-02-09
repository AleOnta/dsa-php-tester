<?php

namespace Backend\Repositories;

use Backend\Models\Dataset;
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
        return $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
    }

    public function create(Dataset $dataset)
    {
        # define the query
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, type, size) VALUES (?, ?, ?);");
        # execute the query
        $insert = $stmt->execute([$dataset->getName(), $dataset->getType(), $dataset->getSize('B')]);
        # check the operation result
        if ($insert) {
            # return dataset id
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function storeJSON(int $datasetId, string $json)
    {
        # define the query
        $stmt = $this->db->prepare("INSERT INTO json_datasets_content (dataset_id, object) VALUES (?, ?);");
        # execute the query binding params
        $insert = $stmt->execute([$datasetId, $json]);
        # return id or failure
        if ($insert) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}
