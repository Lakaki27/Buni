<?php

namespace Buni\Database;

class Team
{
    private array $members = [];
    private Connection $appDb;

    private string $teamName = "";
    private int $teamScrumMasterId = 0;

    public function __construct(private int $teamId)
    {
        $this->appDb = Connection::getInstance();
        $this->teamId = $teamId;

        $teamData = $this->appDb->select("SELECT `name` FROM teams WHERE id = :id", ['id' => $this->teamId]);

        if (isset($teamData[0])) {  
            $this->teamName = $teamData[0]['name'];
        }

        $this->setMembers();
    }

    public function getScrumMaster()
    {
        $userData = $this->appDb->select("SELECT mail, username, first_name, last_name, id FROM users WHERE id = :id", ['id' => $this->teamScrumMasterId])[0];

        $newUser = new User($userData);
        var_dump($newUser);
        return $newUser;
    }

    public function getName(): string
    {
        return $this->teamName;
    }

    public function getTeamId(): int
    {
        return $this->teamId;
    }

    private function setMembers(): void
    {
        $users = $this->appDb->select("SELECT users.mail, users.username, users.first_name, users.last_name, users.id
        FROM users_teams
        JOIN users ON users_teams.id_users = users.id
        WHERE id_teams = :teamId", ["teamId" => $this->teamId]);

        $members = [];

        foreach ($users as $userData) {
            $members[] = new User($userData);
        }

        $this->members = $members;
    }

    public function getMembers(): array
    {
        return $this->members;
    }
}
