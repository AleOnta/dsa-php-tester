<?php

namespace Backend\Repositories;

use PDO;
use Backend\Models\ApiKey;

class ApiKeyRepository extends \Backend\Repositories\Repository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'api_keys');
    }

    public function findByUserId(int $user_id)
    {
        # prepare the query
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id;");
        # bind the params
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        # execute the query
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
        # return the data
        if ($data) {
            $apikey = new ApiKey();
            return $apikey->hydrate($data);
        }
        return false;
    }

    public function storeApiKey(int $user_id, string $hash)
    {
        # prepare the query
        $query = "INSERT INTO {$this->table} (user_id, api_key, created_at, expires_at, is_active) VALUES (?, ?, ?, ?, ?);";
        $stmt = $this->db->prepare($query);
        # bind the parameters and execute the query
        $res = $stmt->execute([$user_id, $hash, date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('+ 4 hours')), true]);
        # return result
        if ($res) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}
