<?php

require $_SERVER['DOCUMENT_ROOT']."/include/floatingMenu.php";

use Buni\Database\User;

$user = new User($_SESSION['userInfo']);

$teams = $user->getTeams();
?>

<div class="p-2 position-fixed">
    <button class="btn btn-primary btn-lg"><i class="fa-solid fa-bars"></i></button>
    <div class="menu">
      <ul>
        <li>accueil</li>
        <li>équipes</li>
      </ul>  
    </div>
</div>

<div class="container" id="pageContainer">
    <div class="p-2 d-flex justify-content-center align-items-center">
        <img id="imgLogo" src="/assets/img/buni-logo.png" alt="Buni">
        <h1 style="font-size: 65px;">Buni</h1>
    </div>

    <div class="grid gap-3 d-flex justify-content-center align-items-center">
        <button type="button" class="btn btn-primary btn-lg p-3">Créer une équipe</button>
    </div>

    <div class="container-sm text-center bg-white rounded-3 shadow-sm p-3">
        <h1>Mes équipes</h1>
        <div class="row g-2 g-lg-3">
            <? foreach ($teams as $team): ?>
                <div class="col-4">
                    <div class="p-3 bg-secondary rounded-3">
                        <h2><?= $team->getName(); ?></h2>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>

<script src="/modules/home/assets/js/main.js"></script>