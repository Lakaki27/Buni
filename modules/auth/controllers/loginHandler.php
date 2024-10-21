<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

use Buni\Auth\AuthRequest;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["mail"]) && isset($data["password"])) {
        $email = strtolower(trim($data["mail"]));
        $inputPass = trim($data["password"]);

        $authRequest = new AuthRequest($email, $inputPass);
        $verifiedRequest = $authRequest->verifyAuthValidity();

        if ($verifiedRequest["valid"]) {
            $authRequest->auth();
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
        echo json_encode(["error" => "Missing auth informations."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
