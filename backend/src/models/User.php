<?php

namespace Backend\Models;

class User
{

    public ?int $id;
    public string $username;
    public string $email;
    public string $password;
    public string $created_at;

    public function __construct(int $id, string $username, string $email, string $password, string $created_at)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
