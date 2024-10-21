<?php

namespace Buni\Auth;

use Exception;
use Buni\Database\Connection;
use Buni\Database\User;

class AuthRequest
{
    private string $mail = "";
    private string $password = "";
    private array $foundUser = [];

    private UserAvailabilityVerificator $uav;
    private Connection $appDb;

    public function __construct(string $email, string $password)
    {
        $this->mail = trim($email);
        $this->password = trim($password);
        $this->uav = UserAvailabilityVerificator::getInstance();
        $this->appDb = Connection::getInstance();
    }

    public function verifyAuthValidity(): array
    {
        try {
            if (!$this->isValidMail()) {
                return ["valid" => false, "message" => "Unknown mail address."];
            }

            if (!$this->isValidPassword()) {
                return ["valid" => false, "message" => "Invalid password."];
            }

            $stmt = $this->appDb->select(
                "SELECT id, username, first_name, last_name, mail, active, password FROM users WHERE mail =   :email",
                ["email" => $this->mail]
            );

            if ($stmt === []) {
                return ["valid" => false, "message" => "Unknown mail address."];
            }

            if (
                !(
                    $stmt[0] &&
                    password_verify($this->password, $stmt[0]["password"])
                )
            ) {
                return ["valid" => false, "message" => "Invalid password."];
            }

            if ($stmt[0]["active"] !== 1) {
                return ["valid" => false, "message" => "Account not found."];
            }

            unset($stmt[0]["password"]);

            $this->setFoundUser($stmt[0]);

            return ["valid" => true, "message" => "Logged in successfully !"];
        } catch (Exception $e) {
            return ["valid" => false, "message" => $e];
        }
    }

    private function setFoundUser($user)
    {
        $this->foundUser = $user;
    }

    private function getFoundUser(): array
    {
        return $this->foundUser;
    }

    public function auth(): void
    {
        $foundUser = $this->getFoundUser();

        $_SESSION['userInfo'] = [
            'mail' => $foundUser["mail"],
            'username' => $foundUser["username"],
            'firstName' => $foundUser["first_name"],
            'lastName' => $foundUser["last_name"],
            'dbId' => $foundUser["id"]
        ];
    }

    private function isValidPassword(): bool
    {
        if (!isset($this->password)) {
            return false;
        }

        return true;
    }

    private function isValidMail(): bool
    {
        if (!isset($this->mail)) {
            return false;
        }

        return true;
    }
}
