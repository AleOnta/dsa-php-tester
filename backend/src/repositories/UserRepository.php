<?php

namespace Backend\Repositories;

class UserRepository extends Repository
{

    public function __construct(\PDO $db)
    {
        # call repository constructor with DB connection from DI container
        parent::__construct($db, 'users');
    }

    private function hydrate(mixed $data): mixed
    {
        if (isset($data['id'])) {
            return new \Backend\Models\User(
                $data['id'],
                $data['username'],
                $data['email'],
                $data['password'],
                $data['created_at']
            );
        }
        return false;
    }

    private function hydrateCollection(array $data): array
    {
        if (count($data) > 0) {
            $return = [];
            array_map(fn($el) => $this->hydrate($el), $data);
        }
        return [];
    }

    public function existsByEmail(string $email)
    {
        # prepare the query
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = :email;");
        # bind the email param
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        # execute the statement
        $stmt->execute();
        # return the result
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?? false;
    }

    public function existsByUsername(string $username)
    {
        # prepare the query
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE username = :username;");
        # bind the username param
        $stmt->bindParam('username', $username, \PDO::PARAM_STR);
        # execute the query
        $stmt->execute();
        # return the result
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?? false;
    }

    public function createUser(string $username, string $email, string $hash)
    {
        # prepare the query
        $query = "INSERT INTO {$this->table} (username, email, password, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP);";
        $stmt = $this->db->prepare($query);
        # execute the query
        $res = $stmt->execute([$username, $email, $hash]);
        # return user id (else PDOException is thrown)
        if ($res) {
            return $this->db->lastInsertId();
        }
    }
}
