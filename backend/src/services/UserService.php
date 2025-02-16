<?php

namespace Backend\Services;

use Backend\Exceptions\AuthenticationException;
use Backend\Exceptions\InvalidRequestException;
use Backend\Exceptions\NotFoundException;
use Backend\Exceptions\ValidationException;
use Backend\Repositories\UserRepository;
use InvalidArgumentException;
use RuntimeException;

class UserService
{

    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    public function validateUserId(int $id)
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new NotFoundException('No user is associated with the forwarded id.');
        }
        return $user['id'];
    }

    public function validateUsername(string $username)
    {
        # check if username is empty string
        if (empty($username)) {
            throw new ValidationException('Data is invalid.', ['username' => 'Username is required']);
        }
        # check if the username contains forbidden chars
        if (!preg_match('/^[0-9a-zA-Z]{5,16}$/', $username)) {
            throw new ValidationException('Data is invalid.', ['username' => 'Username is invalid (must be an alphanumeric string with no special characters and a length between 6-16 chars)']);
        }
        # check if the username has already been taken
        if ($this->userRepo->existsByUsername($username)) {
            $error = 'Username cannot be processed, try to change its value with an alphanumeric string with a length between 6-16 chars';
            throw new InvalidRequestException('Bad Request', ['username' => $error]);
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
            throw new ValidationException(
                'Errors encountered in request body',
                ['email' => "Email address is required"]
            );
        }
        # validate basic email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(
                'Errors encountered in request body',
                ['email' => "Provide a valid email password [Bad email format]"]
            );
        }
        # validate MX
        $domain = explode('@', $email)[1];
        if (!checkdnsrr($domain, 'MX')) {
            throw new ValidationException(
                'Errors encountered in request body',
                ['email' => "MX DNS record for domain ({$domain}) does not exists"]
            );
        }
        # check if the email has already been registered
        if ($this->userRepo->existsByEmail($email)) {
            throw new InvalidRequestException(
                'Bad Request',
                ['email' => 'Email address cannot be processed...']
            );
        }
        return $email;
    }

    # method that validate a user password during registration
    public function validatePassword(string $password)
    {
        # check if password is an empty string
        if (empty($password)) {
            throw new ValidationException(
                'Errors encountered in request body',
                ['password' => 'Password is required']
            );
        }
        # check password length
        if (strlen($password) < 10) {
            throw new ValidationException(
                'Errors encountered in request body',
                ['password' => 'Password length must be over 9 characters']
            );
        }
        # check for at least:
        # strlen >= 10
        # 1 uppercase
        # 1 lowercase
        # 1 number
        # 1 special char
        if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{10,}$/", $password)) {
            throw new ValidationException(
                'Errors encountered in request body',
                ['password' => 'Password is invalid, rules: min 10 chars, min 1 uppercase, min 1 lowercase, min 1 number, min 1 special char.']
            );
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

    public function getUserIdByUsername(string $username)
    {
        $user_id = $this->userRepo->existsByUsername($username);
        if (!$user_id) {
            throw new NotFoundException("username '{$username}' is invalid.");
        }
        return $user_id;
    }

    public function create(string $username, string $email, string $password): int
    {
        # hash the password
        $hash = password_hash($password, PASSWORD_BCRYPT);
        # create the user and return id
        return $this->userRepo->createUser($username, $email, $hash);
    }

    public function update(array $updates)
    {
        # extract the required variables
        $query = implode(', ', $updates['query']);
        $values = $updates['values'];
        # update the user
        if (!$this->userRepo->updateUser($query, $values)) {
            throw new RuntimeException('Error while updating the user entity. Try again later');
        }
    }

    public function authenticateWithPassword(int $userId, string $password)
    {
        # retrieved the hashed password
        $hash = $this->getHashedPassword($userId);
        # verify the match
        if (!password_verify($password, $hash)) {
            throw new AuthenticationException(['password' => 'The provided password doesn\'t match our records.']);
        }
        return true;
    }
}
