<?php

use Buni\Database\Team;
use Buni\Database\User;

$user = new User($_SESSION['userInfo']);
try {
    $team = new Team(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/scrums/{$_GET['id']}.json"), true)["teamId"]);
} catch (\Throwable $th) {
    echo "<script>window.location.replace('/')</script>";
}

$userIsScrumMaster = ($team->getScrumMaster()->getDbId() === $user->getDbId());
?>

<div id="contentDiv"></div>

<script src="/modules/scrums/assets/js/main.js"></script>

<?php if ($userIsScrumMaster) {
    echo <<<HTML
    <script src="/modules/scrums/assets/js/scrumMasterActions.js"></script>
    HTML;
}

?>