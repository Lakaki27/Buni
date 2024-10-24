<?php

use Buni\Database\Connection;
use Buni\Database\User;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["inviteId"]) && isset($data['accept'])) {

        $appDb = Connection::getInstance();
        $user = new User($_SESSION['userInfo']);

        $userInvite = $appDb->select('SELECT id FROM users_teams WHERE id_users = :userId AND id_teams = :teamId', [
            'userId' => $user->getDbId(),
            'teamId' => $data['inviteId']
        ]);

        if ($data['accept'] === true) {
            $appDb->update('users_teams', ['status' => 'accepted'], "id = {$userInvite[0]['id']}");
            header("Content-Type: application/json; charset=utf-8");
            return;
        } else if ($data['accept'] === false) {
            $appDb->delete('users_teams', 'id = :id_users_teams', ['id_users_teams' => $userInvite[0]['id']]);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success" => "Invite denied."
            ]);
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
