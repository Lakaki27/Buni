<?php

namespace Buni\Database;

class User
{
    private $dbId;
    private $username;
    private $firstName;
    private $lastName;
    private $mail;

    private ?Connection $appDb = null;

    /**
     *@param $userData
     * */
    public function __construct(array $userData)
    {
        $this->mail = $userData["mail"];
        $this->username = $userData["username"];
        $this->firstName = isset($userData["firstName"]) ? $userData['firstName'] : $userData['first_name'];
        $this->lastName = isset($userData["lastName"]) ? $userData['lastName'] : $userData['last_name'];
        $this->dbId = isset($userData["dbId"]) ? $userData['dbId'] : $userData['id'];
        $this->appDb = Connection::getInstance();
    }

    public function getUserInfos(string $key = ""): array|string|int
    {
        $userInfos = [
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "mail" => $this->mail,
            "dbId" => $this->dbId,
            "username" => $this->username,
        ];

        if ($key !== "") {
            return $userInfos[$key];
        } else {
            return $userInfos;
        }
    }

    public function getDbId(): int
    {
        return $this->dbId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function getTeams(): array
    {
        $userTeams = $this->appDb->select("SELECT teams.name, teams.id, teams.created_at
        FROM users_teams
        JOIN teams on users_teams.id_teams = teams.id
        WHERE users_teams.id_users = :id", ["id" => $this->dbId]);

        $teams = [];

        foreach ($userTeams as $team) {
            $teams[] = new Team($team["id"]);
        }

        return $teams;
    }
}