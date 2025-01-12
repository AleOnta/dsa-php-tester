<?php

namespace Backend\Repositories;

class UserRepository extends Repository {

    public function __construct(\PDO $db) {
        # call repository constructor with DB connection from DI container
        parent::__construct($db, 'users');
    }

    public function existsByEmail(string $email) {
        # prepare the query
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = :email;");
        # bind the email param
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        # execute the statement
        $stmt->execute();
        # return the result
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?? false;
    }

    public function existsByUsername(string $username) {
        # prepare the query
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE username = :username;");
        # bind the username param
        $stmt->bindParam('username', $username, \PDO::PARAM_STR);
        # execute the query
        $stmt->execute();
        # return the result
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}