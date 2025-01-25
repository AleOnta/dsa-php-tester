<?php

namespace Backend\Services;

use Backend\Repositories\UserRepository;

class UserService
{

    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    public function validateUsername(string $username)
    {
        # check if username is empty string
        if (empty($username)) {
            return [false, 'Username is required'];
        }
        # check if the username contains forbidden chars
        if (!preg_match('/^[0-9a-zA-Z]{5,16}$/', $username)) {
            return [false, 'Username is invalid (must be an alphanumeric string with no special characters and a length between 6-16 chars)'];
        }
        return $username;
    }

    # method that validate a user email during registration
    public function validateEmail(string $email)
    {
        # clear email from whitespaces
        $email = trim($email);
        # check if email is an empty string
        if (empty($email)) {
            return [false, 'Email address is required'];
        }
        # validate basic email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'Provide a valid email password [Bad email format]'];
        }
        # validate MX
        $domain = explode('@', $email)[1];
        if (!checkdnsrr($domain, 'MX')) {
            return [false, "MX DNS record for domain ({$domain}) does not exists"];
        }
        return $email;
    }

    # method that validate a user password during registration
    public function validatePassword(string $password)
    {
        # check if password is an empty string
        if (empty($password)) {
            return [false, 'Password is required'];
        }
        # check password length
        if (strlen($password) < 10) {
            return [false, 'Password length must be over 9 characters'];
        }
        # check for at least:
        # strlen >= 10
        # 1 uppercase
        # 1 lowercase
        # 1 number
        # 1 special char
        if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{10,}$/", $password)) {
            $message = 'Password is invalid,';
            $message .= ' rules: min 10 chars, min 1 uppercase, min 1 lowercase, min 1 number, min 1 special char.';
            return [false, $message];
        }
        return true;
    }

    public function emailExists(string $email)
    {
        return $this->userRepo->existsByEmail($email);
    }

    public function usernameExists(string $username)
    {
        return $this->userRepo->existsByUsername($username);
    }

    public function getHashedPassword(int $user_id)
    {
        return $this->userRepo->getUserPassword($user_id);
    }

    public function create(string $username, string $email, string $password): int
    {
        # hash the password
        $hash = password_hash($password, PASSWORD_BCRYPT);
        # create the user and return id
        return $this->userRepo->createUser($username, $email, $hash);
    }
}
