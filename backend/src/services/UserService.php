<?php

namespace Backend\Services;

use Backend\Repositories\UserRepository;

class UserService {

    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepository) {
        $this->userRepo = $userRepository;
    }

    # method that validate a user email during registration
    public function validateEmail(string $email) {
        # clear email from whitespaces
        $email = trim($email);
        # check if email is an empty string
        if (empty($email)) {
            return [false, 'Email address is required'];
        } 
        # validate basic email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'Provide a valid email password'];
        }
        # validate MX
        if (checkdnsrr(explode('@', $email)[1])) {
            return [false, 'Email domain does not exists'];
        }
        return true;
    }

    # method that validate a user password during registration
    public function validatePassword(string $password) {
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
            $message = 'Password is invalid'.PHP_EOL;
            $message.= 'rules: min 10 chars, min 1 uppercase, min 1 lowercase, min 1 number, min 1 special char.';
            return [false, $message];
        }
        return true;
    }
}