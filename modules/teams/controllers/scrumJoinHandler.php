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

        $userIsTeamMember = false;
        foreach ($team->getMembers() as $member) {
            if ($member->getDbId() === $user->getDbId()) {
                $userIsTeamMember = true;
                break;
            }
        }

        if (!$userIsTeamMember) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Permission denied."]);
            return;
        }

        $lastTeamScrum = $team->getLastScrum();

        if ($lastTeamScrum === []) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "No scrum is currently running."]);
            return;
        }

        $scrumFileId = $lastTeamScrum['uuid'];
        $path = $_SERVER['DOCUMENT_ROOT'] . "/scrums/{$scrumFileId}.json";

        if (!file_exists($path)) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "No scrum is currently running."]);
            return;
        }

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["success" => $scrumFileId]);
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
