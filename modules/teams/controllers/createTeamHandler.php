<?php

use Buni\Database\Connection;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["name"]) && isset($data["teamId"])) {
        if (!preg_match('/[a-zA-Z0-9]{3,30}/', trim($data['name']))) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "The team name may only contain from 3 to 30 characters, with only letters and numbers.",
            ]);
            return;
        }

        $appDb = Connection::getInstance();
        $teams = $appDb->select("SELECT * FROM teams WHERE teams.name = :name", ["name" => trim($data['name'])]);

        if (count($teams) !== 0) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "Team name is already taken.",
            ]);
            return;
        }

        $createdTeam = $appDb->insert('teams', ['id_scrum_master' => $_SESSION['userInfo']['dbId'], 'id_trello_board' => $data['teamId'], 'name' => trim($data['name'])]);

        if ($createdTeam) {
            $createdUserTeamLink = $appDb->insert('users_teams', ['id_users' => $_SESSION['userInfo']['dbId'], 'id_teams' => $createdTeam['ids'], 'status' => 'accepted']);
            if ($createdUserTeamLink) {
                $_SESSION['messageToToast'] = [
                    "icon" => "success",
                    "text" => "Team created successfully !"
                ];

                header("Content-Type: application/json; charset=utf-8");
                echo json_encode([
                    "success" => "Team created successfully !"
                ]);
                return;
            } else {
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode(["error" => "Error creating team."]);
                return;
            }
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Error creating team."]);
            return;
        }

        
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Missing team name."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
