<?php

namespace Buni\Auth;

use Exception;
use Buni\Database\Connection;

class NewAccountRequest
{
    private string $username = "";
    private string $firstName = "";
    private string $lastName = "";
    private string $mail = "";
    private string $password = "";
    private string $trelloId = "";


    private UserAvailabilityVerificator $uav;
    private Connection $appDb;

    public function __construct(array $userData)
    {
        $this->mail = trim($userData['mail']);
        $this->trelloId = trim($userData['trelloId']);
        $this->password = trim($userData['password']);
        $this->firstName = trim($userData['firstName']);
        $this->lastName = trim($userData['lastName']);
        $this->username = trim($userData['username']);
        $this->uav = UserAvailabilityVerificator::getInstance();
        $this->appDb = Connection::getInstance();
        $this->trelloId = trim($userData['trelloId']);
    }

    public function verifyRegisterValidity(): array
    {
        try {
            if (!$this->isValidMail()) {
                return ["valid" => false, "message" => "Invalid e-mail address."];
            }

            if (!$this->isValidPassword()) {
                return ["valid" => false, "message" => "Invalid password."];
            }

            if (!$this->isValidUsername()) {
                return ["valid" => false, "message" => "Invalid username."];
            }

            if (!$this->isValidLastName()) {
                return ["valid" => false, "message" => "Invalid last name."];
            }

            if (!$this->isValidFirstName()) {
                return ["valid" => false, "message" => "Invalid first name."];
            }

            if ($this->userExists()) {
                return ["valid" => false, "message" => "E-mail address is already taken."];
            }

            return ["valid" => true, "message" => "Account has been created successfully !"];
        } catch (Exception $e) {
            return ["valid" => false, "message" => $e];
        }
    }

    private function newUserInfosToArray(): array
    {
        return [
            "mail" => $this->mail,
            "username" => $this->username,
            "password" => password_hash($this->password, PASSWORD_BCRYPT),
            "first_name" => $this->firstName,
            "last_name" => $this->lastName,
            "id_trello" => $this->trelloId,
        ];
    }

    public function registerUser(): bool
    {
        try {
            $this->appDb->insert("users", $this->newUserInfosToArray());
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function userExists(): bool
    {
        return !$this->uav->verifyNewUserAvailability([
            "email" => $this->mail,
        ]);
    }

    private function isValidPassword(): bool
    {
        if (!isset($this->password) || empty($this->password)) {
            return false;
        }

        return true;
    }

    private function isValidMail(): bool
    {
        if (!isset($this->mail) || empty($this->mail)) {
            return false;
        }

        return true;
    }

    private function isValidUsername(): bool
    {
        if (!isset($this->username) || empty($this->username)) {
            return false;
        }

        return true;
    }

    private function isValidFirstName(): bool
    {
        if (!isset($this->firstName) || empty($this->firstName)) {
            return false;
        }

        return true;
    }

    private function isValidLastName(): bool
    {
        if (!isset($this->lastName) || empty($this->lastName)) {
            return false;
        }

        return true;
    }
}
