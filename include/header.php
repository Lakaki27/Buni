<?php

use Buni\Database\User;

session_start();

require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";

$userData = [];
$user = null;

if (!preg_match("(login|controllers|assets|logout)", $_SERVER["REQUEST_URI"])) {
    if (!isset($_SESSION["userInfo"])) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://" . $_SERVER["HTTP_HOST"] . "/login");
        exit();
    } else {
        $userData = $_SESSION['userInfo'];
        $user = new User($userData);
    }
}
