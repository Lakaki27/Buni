<?php

namespace Buni\Auth;

use Buni\Database\Connection;

class UserAvailabilityVerificator {
    private static ?UserAvailabilityVerificator $_instance = null;
    
    private function __construct(
        private Connection $appDb
    ){}

    public static function getInstance(): UserAvailabilityVerificator {
        if (self::$_instance === null) {
            self::$_instance = new self(Connection::getInstance());
        }

        return self::$_instance;
    }

    public function verifyNewUserAvailability(array $u) {
        if ($this->mailAlreadyExists($u['email'])) {
            return false;
        }
        
        return true;
    }

    // private function usernameAlreadyExists($u)
    // {
    //     $stmt = $this->appDb->select("*", "users", "username = :username", ['username' => trim($u)]);
    //     return $stmt !== [];
    // }

    private function mailAlreadyExists($m)
    {
        $stmt = $this->appDb->select("SELECT * FROM users WHERE mail = :email", ['email' => trim($m)]);
        return $stmt !== [];
    }
}