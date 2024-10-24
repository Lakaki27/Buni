<?php

use Buni\Database\Connection;
use Buni\Database\Team;
use Buni\Database\User;
use Buni\Scrums\DailyScrum;
use Buni\TrelloAPI\TrelloAPIRequest;

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/header.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    if (isset($data["scrumId"])) {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/scrums/{$data['scrumId']}.json")) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(["error" => "Scrum has ended."]);
            return;
        }

        $appDb = Connection::getInstance();
        $trelloAPI = TrelloAPIRequest::getInstance();
        $user = new User($_SESSION['userInfo']);
        $scrum = new DailyScrum(["scrumId" => $data["scrumId"]]);
        $scrumData = $scrum->getScrumData();
        $team = new Team($scrumData['teamId']);

        $userIsScrumMaster = ($team->getScrumMaster()->getDbId() === $user->getDbId());
        $addNextStepBtn = true;
        $nextStepBtnText = "Continue";
        $changeRefreshState = "";

        $html = "";

        switch ($scrumData["state"]) {
            case "init":
                $html = <<<HTML
                        <h1>Scrum master is speaking</h1>
                        HTML;
                break;

            case "voting":
                $userHasVoted = (in_array($user->getTrelloId(), $scrumData["votedUsers"]));
                $html = <<<HTML
                            <div class="container">
                            <h1>Time to vote !</h1>
                            HTML;
                if (!$userHasVoted) {
                    foreach ($scrumData["tasks"] as $taskId => $task) {
                        $html .= "
                                <div>
                                    <h3>{$task['name']}</h3>
                                </div>
                                <select class='voteSelect' id='{$taskId}'>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='5'>5</option>
                                    <option value='8'>8</option>
                                    <option value='13'>13</option>
                                    <option value='Question'>Question</option>
                                    <option value='Coffee'>Coffee</option>
                                </select>
                            ";
                        $changeRefreshState = "stop";
                    }
                    $html .= "<div><button class='btn btn-primary' id='voteBtn'>Submit</button></div></div>";
                    $addNextStepBtn = false;
                } else {
                    $html .= "<h2>Your vote has been submitted.</h2></div>";
                }
                break;

            case "attributing":
                if ($userIsScrumMaster) {
                    $html = <<<HTML
                        <div class="container">
                        <h1>Grant tasks to users: </h1>
                        HTML;
                    foreach ($scrumData["tasks"] as $taskId => $task) {
                        $html .= "
                        <div>
                            <h3>{$task['name']}</h3>
                        </div>
                        <div>
                        <select class='attributeSelect' id='{$taskId}'>";
                        foreach ($task["votes"] as $voterId => $vote) {
                            $html .= "
                            <option value='{$voterId}'>$vote</option>";
                        }
                        $html .= "</select></div>";
                    }
                    $html .= "<div><button id='attributeBtn' class='btn btn-primary'>Confirm</button></div></div>";
                    $addNextStepBtn = false;
                    $changeRefreshState = "stop";
                } else {
                    $html = <<<HTML
                        <h1>Scrum master is attributing tasks...</h1>
                        HTML;
                }
                break;

            case "closing":
                $html = <<<HTML
                        <div class="container">
                        <h1>Final task attribution: </h1>
                    HTML;

                foreach ($scrumData['tasks'] as $task) {
                    $html .= "
                    <div><h3>{$task['name']}</h3> was attributed to {$task['attributedUser']['name']}</div>";
                }
                $html .= "</div>";
                break;
        }

        if ($userIsScrumMaster && $addNextStepBtn) {
            $html .= <<<HTML
            <div class="container">
                <button id="nextStep" class="btn btn-primary">$nextStepBtnText</button>
            </div>
            HTML;
        }

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["success" => $html, "changeRefreshState" => $changeRefreshState]);
        return;
    } else {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["error" => "Missing team name."]);
        return;
    }
} else {
    header("Location: /");
    exit();
}
