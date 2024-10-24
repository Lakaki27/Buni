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

    if (isset($data["scrumId"]) && (isset($data["votes"]) || isset($data['attributions']))) {
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

        $newScrumData = $scrumData;

        if ($data["voteType"] === "attribute") {
            foreach ($data["attributions"] as $cardId => $userData) {
                $newScrumData["tasks"][$cardId]["attributedUser"] = $userData;
            }
        } else if ($data["voteType"] === "vote") {
            $newScrumData["votedUsers"][] = $user->getTrelloId();

            foreach ($data["votes"] as $cardId => $vote) {
                $newScrumData["tasks"][$cardId]["votes"][$user->getTrelloId()] = $user->getName() . " (has rated this card: " . $vote . ")";
            }
        }

        $scrum->updateScrum($data['scrumId'], $newScrumData);

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["success" => "Votes submitted !", "changeRefreshState" => "restart"]);
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
