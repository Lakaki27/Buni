<?php

use Buni\Database\Connection;
use Buni\Database\Team;
use Buni\Database\User;
use Buni\Scrums\DailyScrum;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["teamName"])) {
        $stmt = Connection::getInstance();

        $foundTeam = $stmt->select("SELECT id FROM teams WHERE name = :teamName", ["teamName" => $data['teamName']]);

        if (!isset($foundTeam[0])) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Missing team name."]);
            return;
        }

        $team = new Team($foundTeam[0]['id']);
        $user = new User($_SESSION['userInfo']);

        $userIsScrumMaster = ($team->getScrumMaster()->getDbId() === $user->getDbId());

        if (!$userIsScrumMaster) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Permission denied."]);
            return;
        }

        $lastTeamScrum = $team->getLastScrum();

        if ($lastTeamScrum !== []) {
            if (
                !(floor(
                    (time() - strtotime($lastTeamScrum['created_at'])) / 86400
                ) >= 1)
            ) {
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode(["error" => "There has already been a daily scrum in the last 24hours."]);
                return;
            }
        }

        $dailyScrum = new DailyScrum(["team" => $team, "scrumMaster" => $user]);

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["success" => $dailyScrum->getScrumFileId()]);
        return;
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Invalid team."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
