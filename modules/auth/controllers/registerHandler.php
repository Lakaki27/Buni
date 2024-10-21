<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

use Buni\Auth\NewAccountRequest;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["mail"]) && isset($data["password"]) && isset($data["confirmPassword"]) && isset($data["firstName"]) && isset($data["lastName"]) && isset($data["username"])) {
        if ($data['confirmPassword'] !== $data['password']) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => "Passwords do not match.",
            ]);
            return;
        }

        $registerRequest = new NewAccountRequest($data);
        $verifiedRequest = $registerRequest->verifyRegisterValidity();

        if ($verifiedRequest["valid"]) {
            $registerRequest->registerUser();
            $_SESSION['messageToToast'] = [
                "icon" => "success",
                "text" => $verifiedRequest["message"]
            ];
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success" => $verifiedRequest["message"],
            ]);
            return;
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "error" => $verifiedRequest["message"],
            ]);
            return;
        }
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Missing register information."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}