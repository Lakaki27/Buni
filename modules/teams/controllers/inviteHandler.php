<?php

use Buni\Database\Connection;
use Buni\Database\User;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["mail"]) || isset($data['teamName'])) {
        if (!preg_match('/\S+@\S+\.\S+/', trim($data['mail']))) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "Invalid e-mail address !",
            ]);
            return;
        }

        $appDb = Connection::getInstance();
        $user = new User($_SESSION['userInfo']);

        $scrumMasterTeamId = false;

        foreach ($user->getTeams()["accepted"] as $team) {
            if ($data['teamName'] === $team->getName()) {
                $scrumMasterTeamId = $team->getTeamId();
                break;
            }
        };

        if (!$scrumMasterTeamId) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "Permission denied !",
            ]);
            return;
        }

        $isUserMember = $appDb->select("SELECT users.*, users_teams.status
        FROM users
        JOIN users_teams ON users.id = users_teams.id_users AND users_teams.id_teams = :teamId
        WHERE mail = :mail", ["mail" => trim($data['mail']), "teamId" => $scrumMasterTeamId]);

        if (count($isUserMember) !== 0) {
            if ($isUserMember[0]['status'] === "ongoing") {
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode([
                    "error" => "User already has a pending invite for this team !"
                ]);
                return;
            } else if ($isUserMember[0]['status'] === "accepted") {
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode([
                    "error" => "User is already a member of this team !"
                ]);
                return;
            }
        }

        $userToInvite = $appDb->select('SELECT id FROM users WHERE mail = :mail', ['mail' => trim($data['mail'])]);

        if (count($userToInvite) === 0) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "E-mail does not belong to a registered Buni user !"
            ]);
            return;
        }

        $invitedUser = $appDb->insert('users_teams', ['id_users' => $userToInvite[0]["id"], 'id_teams' => $scrumMasterTeamId, 'status' => 'ongoing']);

        if ($invitedUser) {
            mail(trim($data['mail']), "New invite !", "You have received a new invite on Buni.");
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success" => "Invite sent successfully !",
            ]);
            return;
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Unable to invite user."]);
            return;
        }
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Invalid data."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
