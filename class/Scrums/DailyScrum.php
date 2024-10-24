<?php

namespace Buni\Scrums;

use Buni\Database\Connection;
use Buni\Database\Team;
use Buni\Database\User;
use Buni\TrelloAPI\TrelloAPIRequest;
use Exception;

class DailyScrum
{
    private string $fileDir;
    private Team $team;
    private $scrumMaster;
    private $scrumFileId;
    private $appDb;
    private ?TrelloAPIRequest $trelloApi = null;

    public function __construct(array $data)
    {
        $this->appDb = Connection::getInstance();
        $this->trelloApi = TrelloAPIRequest::getInstance();
        $this->fileDir = $_SERVER['DOCUMENT_ROOT'] . "/scrums";
        if (isset($data['team']) && isset($data['scrumMaster'])) {
            $this->setTeam($data['team']);
            $this->setScrumMaster($data['scrumMaster']);
            $this->createScrum();
        } else if (isset($data['scrumId'])) {
            $this->buildFromScrumFile($data['scrumId']);
            $this->setScrumFileId($data['scrumId']);
        }
    }

    private function findScrumFile(string $fileId)
    {
        if (file_exists($this->fileDir . "/{$fileId}.json")) {
            return $this->fileDir . "/{$fileId}.json";
        } else {
            return false;
        }
    }

    private function readScrumFile(string $fileId)
    {
        $file = $this->findScrumFile($fileId);

        if ($file) {
            $f = fopen($file, 'r');
            $data = fread($f, filesize($file));
            fclose($f);
            return json_decode($data, true);
        }
    }

    private function buildFromScrumFile(string $fileId)
    {
        $data = $this->readScrumFile($fileId);
        $this->setTeam(new Team($data['teamId']));
        $this->setScrumMaster($this->getTeam()->getScrumMaster());
    }

    private function writeToScrumFile(string $fileId, array $data)
    {
        $file = $this->findScrumFile($fileId);

        if ($file) {
            $f = fopen($file, 'w');
            fwrite($f, json_encode($data));
            fclose($f);
        }
    }

    public function createScrum()
    {
        $id = $this->insertScrum();
        $this->setScrumFileId($id);
        $this->createScrumFile($id, $this->getTeam());
    }

    private function createScrumFile($fileId, Team $team)
    {
        if (!$this->findScrumFile($fileId)) {
            $f = fopen($this->fileDir . "/{$fileId}.json", 'w+');
            fwrite($f, json_encode(["boardId" => $team->getTeamTrelloID(), "state" => "init", "teamId" => $team->getTeamId(), "listIds" => $this->getSubjectsAndPlannedLists(), "votedUsers" => [], "tasks" => $this->getInitScrumTasks()]));
            fclose($f);
        }
    }

    private function getSubjectsAndPlannedLists()
    {
        $listsRaw = $this->trelloApi->getAllLists($this->team->getTeamTrelloID());
        
        $listLabels = [
            "Sujets du prochain Daily",
            "En cours",
        ];

        $lists = [];
        foreach ($listsRaw as $listName => $listId) {
            if (in_array($listName, $listLabels)) {
                $lists[$listName] = $listId;
            }
        }

        return $lists;
    }

    private function getInitScrumTasks()
    {
        $tasksRaw = $this->trelloApi->getAllCards($this->team->getTeamTrelloID());
        $listsRaw = $this->trelloApi->getAllLists($this->team->getTeamTrelloID());

        $listLabels = [
            "Sujets du prochain Daily",
            "A faire",
            "En cours",
            "Fini"
        ];

        $lists = [];
        $tasks = [];

        foreach ($listsRaw as $listName => $listId) {
            if (in_array($listName, $listLabels)) {
                $lists[$listName] = $listId;
            }
        }

        foreach ($tasksRaw as $task) {
            if ($task['listId'] === $lists['Sujets du prochain Daily']) {
                $tasks[$task["id"]] = ["name" => $task['name'], "votes" => []];
            }
        }

        return $tasks;
    }

    private function insertScrum()
    {
        $id = uniqid("", true);
        $this->appDb->insert('daily_scrums', ["id_teams" => $this->getTeam()->getTeamId(), "uuid" => $id]);
        return $id;
    }

    public function updateScrum(string $fileId, array $data)
    {
        $this->writeToScrumFile($fileId, $data);
    }

    public function getCurrentState()
    {
        $scrumInfos = $this->readScrumFile($this->getScrumFileId());

        return $scrumInfos["state"];
    }

    public function getScrumData()
    {
        $scrumInfos = $this->readScrumFile($this->getScrumFileId());

        return $scrumInfos;
    }

    public function endScrum()
    {
        $scrumInfos = $this->getScrumData();

        foreach ($scrumInfos['tasks'] as $cardId => $cardData) {
            $this->trelloApi->addMemberToCard($cardId, $cardData['attributedUser']['trelloId']);
        }

        $this->trelloApi->moveAllCards($scrumInfos['listIds']['Sujets du prochain Daily'], $scrumInfos['listIds']['En cours'], $scrumInfos['boardId']);

        @unlink($_SERVER['DOCUMENT_ROOT']."/scrums/{$this->getScrumFileId()}.json");
        $this->appDb->delete("daily_scrums", "uuid = :id", ['id' => $this->getScrumFileId()]);
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getScrumMaster()
    {
        return $this->scrumMaster;
    }

    public function setScrumMaster($scrumMaster): self
    {
        $this->scrumMaster = $scrumMaster;

        return $this;
    }

    public function getScrumFileId()
    {
        return $this->scrumFileId;
    }

    public function setScrumFileId($scrumFileId): self
    {
        $this->scrumFileId = $scrumFileId;

        return $this;
    }
}
