<?php

namespace Backend\Repositories;

use PDO;

class DatasetsRepository extends Repository
{
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->table = 'datasets';
    }
}
