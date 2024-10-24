<?php

namespace Buni\Database;

class Team
{
    private array $members = [];
    private Connection $appDb;

    private string $teamName = "";
    private int $teamScrumMasterId = 0;
    private string $trelloBoardId = "";

    public function __construct(private int $teamId)
    {
        $this->appDb = Connection::getInstance();
        $this->teamId = $teamId;

        $teamData = $this->appDb->select("SELECT `name`,`id_scrum_master`,`id_trello_board` FROM teams WHERE id = :id", ['id' => $this->teamId]);

        if (isset($teamData[0])) {
            $this->teamName = $teamData[0]['name'];
        }

        $this->teamScrumMasterId = $teamData[0]['id_scrum_master'];
        $this->trelloBoardId = $teamData[0]['id_trello_board'];
        $this->setMembers();
    }

    public function getScrumMaster()
    {
        $userData = $this->appDb->select("SELECT * FROM users WHERE id = :id", ['id' => $this->teamScrumMasterId])[0];

        $newUser = new User($userData);

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
        $users = $this->appDb->select("SELECT users.mail, users.id_trello, users.username, users.first_name, users.last_name, users.id
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

    private function getAllScrums(): array|false
    {
        $scrums = $this->appDb->select("SELECT *
        FROM daily_scrums
        WHERE id_teams = :teamId
        ORDER BY created_at DESC", ["teamId" => $this->teamId]);

        if (isset($scrums[0])) {
            return $scrums;
        } else {
            return [];
        }
    }

    public function getScrums() 
    {
        return $this->getAllScrums();
    }

    public function getLastScrum()
    {
        $scrums = $this->getAllScrums();
        if ($scrums !== []) {
            return $scrums[0];
        } else {
            return [];
        }
    }

    public function hasOngoingScrum()
    {
        $scrumInfo = $this->getLastScrum();
        if ($scrumInfo === []) return false;
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/scrums/{$scrumInfo['uuid']}.json")) {
            return true;
        }
        return false;
    }

    public function getTeamTrelloID()
    {
        return $this->trelloBoardId;
    }
}