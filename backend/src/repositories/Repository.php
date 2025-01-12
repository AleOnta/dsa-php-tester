<?php

namespace Backend\Repositories;

use PDO;

class Repository {

    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db, string $table) {
        $this->db = $db;
        $this->table = $table;
    }

    public function findById(int $id) {
        # prepare the query
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id;");
        # bind id param
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        # execute the query
        $stmt->execute();
        # return the result
        return $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
    }

    public function findAll() {
        # prepare the query
        $stmt = $this->db->prepare("SELECT * FROM {$this->table};");
        # execute the query
        $stmt->execute();
        # return the result
        return $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
    }

}