<?php

namespace Buni\Auth;

use Buni\Database\Connection;

class AuthRequest
{
    private $email;
    private $password;
    private $foundUser;

    public function __construct(string $email, string $password, private UserAvailabilityVerificator $uav, private Connection $appDb)
    {
        $this->email = trim($email);
        $this->password = trim($password);
        $this->uav = UserAvailabilityVerificator::getInstance();
        $this->appDb = Connection::getInstance();
    }

    public function verifyAuthValidity()
    {
        if (!$this->isValidMail()) {
            return ["valid" => false, "message" => "E-mail is invalid !"];
        };

        if (!$this->isValidPassword()) {
            return ["valid" => false, "message" => "Invalid password."];
        }

        $stmt = $this->appDb->select("SELECT * FROM users WHERE email = :email", ["email" => $this->email]);

        if (!($stmt[0] && password_verify($this->password, $stmt[0]['password']))) {

        }

        if ($stmt[0]['active'] !== 1) {
            return ["valid" => false, "message" => "Account not found."];
        }

        $this->foundUser = $stmt[0];

        return ["valid" => true];
    }

    public function auth()
    {
        session_start();
        foreach ($this->foundUser as $key => $value) {
            if ($key !== 'password') {
                $_SESSION[$key] = $value;
            }
        }
        header('Location: /');
        exit();
    }

    public function userExists()
    {
        return !$this->uav->verifyNewUserAvailability([
            "email" => $this->email
        ]);
    }

    private function isValidPassword()
    {
        if (!isset($this->password)) {
            return false;
        }

        return true;
    }

    private function isValidMail()
    {
        if (!isset($this->email)) {
            return false;
        }

        return true;
    }
}
