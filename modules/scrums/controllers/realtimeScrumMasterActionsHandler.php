<?php

use Buni\Database\Connection;
use Buni\Database\Team;
use Buni\Database\User;
use Buni\Scrums\DailyScrum;
use Buni\TrelloAPI\TrelloAPIRequest;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["scrumId"])) {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/scrums/{$data['scrumId']}.json")) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Scrum has ended."]);
            return;
        }

        $appDb = Connection::getInstance();
        $trelloAPI = TrelloAPIRequest::getInstance();
        $user = new User($_SESSION['userInfo']);
        $scrum = new DailyScrum(["scrumId" => $data["scrumId"]]);
        $scrumData = $scrum->getScrumData();
        $team = new Team($scrumData['teamId']);

        $userIsScrumMaster = ($team->getScrumMaster()->getDbId() === $user->getDbId());

        $newScrumData = $scrumData;
        $update = true;

        switch ($scrumData["state"]) {
            case "init":
                $newScrumData["state"] = "voting";
                break;

            case "voting":
                $newScrumData["state"] = "attributing";
                break;
            
            case "attributing":
                $newScrumData["state"] = "closing";
                break;
            
            case "closing":
                $scrum->endScrum();
                $update = false;
                break;
        }
        if ($update) {
            $scrum->updateScrum($data['scrumId'], $newScrumData);
        }

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["success" => "", "changeRefreshState" => "endScrum"]);
        return;
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Missing team name."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
