<?php

use Buni\Database\User;

$user = new User($_SESSION['userInfo']);

$teams = $user->getTeams();

require $_SERVER['DOCUMENT_ROOT'] . "/include/sidebar.php";
?>

<div class="container" id="pageContainer">
    <div class="p-2 d-flex justify-content-center align-items-center">
        <img id="imgLogo" src="/assets/img/buni-logo.png" alt="Buni">
        <h1 style="font-size: 65px;">Buni</h1>
    </div>

<div class="container">
    <button class="btn text-white m-1" id="createTeamBtn">Create new team</button>
</div>

<div class="container text-center rounded-3 shadow-sm p-3 m-3 backgroundContainerMenu">
    <h2 class="p-3">Teams</h2>
    <div class="row g-2 g-lg-3 overflow-y-auto" style="height: 270px;">
            <? foreach ($teams['accepted'] as $team): ?>
                <div class="col-4">
                    <div class="p-3 rounded-3 shadow-sm containerMenu">
                        <h2><?= $team->getName(); ?></h2>
                        <button class="viewTeamBtn btn bg-white rounded-3" id="<?= $team->getName(); ?>"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
</div>

<div class="container text-center rounded-3 shadow-sm p-3 m-3 backgroundContainerMenu">
    <h2>Invites</h2>
    <div id="invites" class="overflow-y-scroll" style="height: 120px;">
        <?php foreach ($teams['ongoing'] as $team): ?>
            <div>
                <ul class="list-group list-group-horizontal d-flex text-start m-3 shadow-sm">
                    <li class="d-flex align-items-center list-group-item flex-fill containerMenu"><?= $team->getName(); ?></li>
                    <li class="list-group-item flex-fill text-end d-flex flex-row justify-content-end containerMenu">
                        <button class="btn bg-success text-white d-flex align-items-center acceptBtn" id="<?= $team->getTeamId() ?>"><i class="fa-solid fa-check"></i> Join</button>
                        <button class="btn bg-danger text-white d-flex align-items-center refuseBtn" id="<?= $team->getTeamId() ?>"><i class="fa-solid fa-x"></i> Deny</button>
                    </li>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="/modules/teams/assets/js/main.js"></script>