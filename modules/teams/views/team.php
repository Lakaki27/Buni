<?php

use Buni\Database\Connection;
use Buni\Database\Team;

if (!isset($_GET['t'])) {
    require_once "modules/security/views/404.php";
    die();
}

$stmt = Connection::getInstance();
$foundTeam = $stmt->select("SELECT id FROM teams WHERE name = :teamName", ["teamName" => $_GET['t']]);

if (!isset($foundTeam[0])) {
    echo "<h1>Team does not exist.</h1>";
    die();
}

$team = new Team($foundTeam[0]['id']);

$userIsScrumMaster = ($team->getScrumMaster()->getDbId() === $user->getDbId());


require $_SERVER['DOCUMENT_ROOT'] . "/include/sidebar.php";
?>
<div class="p-2 d-flex justify-content-center align-items-center">
    <?php if ($team->hasOngoingScrum()) { ?>
    <div class="text-success">
        <div class="spinner-grow m-2" role="status">
            <span class="visually-hidden">The daily scrum has started</span>
        </div>
        <i class="fa-solid fa-people-group m-2" style="font-size: 65px;"></i>
    </div>
    <?php } else { ?>
    <div>
        <i class="fa-solid fa-people-group m-2" style="font-size: 65px;"></i>
    </div>
    <?php } ?>
    <h1 class="m-2" style="font-size: 65px;"><?= $team->getName() ?></h1>
</div>
<div class="d-flex">
    <div class="d-flex container-sm align-items-center flex-column w-50" style="height: 600px;">
        <div class="d-flex card m-3 shadow-sm" style="width: 18rem;">
            <div class="card-body rounded-top-3" style="background-color: #e3e3e3;">
                <h3 class="card-title">Scrum Master</h3>
            </div>
            <ul class="list-group list-group-flush rounded-bottom-3">
                <li class="list-group-item">
                    <h5><?= $team->getScrumMaster()->getName() ?></h5>
                </li>
            </ul>
        </div>
        <div class="d-flex card m-3 shadow-sm" style="width: 18rem;">
            <div class="card-body rounded-top-3" style="background-color: #e3e3e3;">
                <h3 class="card-title">Members</h3>
                <?php if ($userIsScrumMaster): ?>
                <button class="btn btn-primary" id="inviteMember">Invite new members</button>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush rounded-bottom-3">
                <?php foreach ($team->getMembers() as $member): ?>
                    <?php if ($team->getScrumMaster()->getDbId() !== $member->getDbId()): ?>
                        <li class="list-group-item">
                            <h5><?= $member->getName() ?></h5>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="container-sm">
        <div class="d-flex m-3 gap-3">
            <?php if ($userIsScrumMaster): ?>
                <button class="btn btn-primary" id="launchDailyScrum">Launch daily scrum</button>
            <?php endif; ?>
            <button class="btn btn-primary" id="joinDailyScrum">Join daily scrum</button>
        </div>
        <?php if ($team->hasOngoingScrum()) { ?>
            <div class="alert alert-success m-3" role="alert" style="width: 85%;">
                A daily scrum has been started, to join it click on the button Join daily scrum.
            </div>

        <?php } else { ?>
            <div class="alert alert-danger m-3" role="alert" style="width: 85%;">
                No daily scrum has been started.
            </div>
        <?php } ?>
        <div class="d-flex card shadow-sm m-3 flex-fill" style="width: 85%;">
            <div class="card-body rounded-top-3" style="background-color: #e3e3e3;">
                <h3 class="card-title">Last scrum</h3>
            </div>
            <ul class="list-group list-group-flush rounded-bottom-3">
            <?php foreach ($team->getScrums() as $scrum): ?>
                <li class="list-group-item">
                    <h5>
                        <?= str_replace("à", "at", date('l j F Y à H:i', strtotime(str_replace(array('à', 'le', 'de'), array('-', '-', '-'), $scrum['created_at'])))) ?>
                    </h5>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>


<script src="/modules/teams/assets/js/teamView.js"></script>
<?php if ($userIsScrumMaster): ?>
    <script src="/modules/teams/assets/js/scrumMasterBtnBinds.js"></script>
<?php endif; ?>