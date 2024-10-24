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

    <div class="grid gap-3 d-flex justify-content-center align-items-center" style="height: 320px;">
        <a href="/teams?create" class="btn btn-primary btn-lg p-3">Create team</a>
    </div>

    <div class="container-sm text-center rounded-3 shadow-sm p-3 backgroundContainerMenu">
        <h1 class="p-2">Recent scrums</h1>
        <div class="row g-2 g-lg-3 overflow-y-scroll" style="height: 220px;">
            <? foreach ($teams['accepted'] as $team): ?>
                <?php
                $scrum = $team->getLastScrum();
                if ($scrum !== []) {
                ?>
                    <div class="col-4">
                        <div class="p-3 rounded-3 containerMenu">
                            <h2><?= $team->getName() ?></h2>
                            <h3><?= $scrum["created_at"]; ?></h3>
                        </div>
                    </div>

                <?php } ?>
            <? endforeach; ?>
        </div>
    </div>
</div>