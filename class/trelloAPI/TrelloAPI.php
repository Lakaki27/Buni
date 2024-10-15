<?php

namespace Buni\TrelloAPI;

class Request {
    const API_URL = "https://api.trello.com";
    const API_TOKEN = "7399da477dccc44d309c3b0d4a0e66b1";
    const API_KEY = "85d25e95687a41f3399b24b9285ceeef18694db8646db9d12e9e322813096fb1";

    private function fetch(string $url, array $options = []) {
        //fetch l'api trello
    }

    private function moveAllCards(string $idSource, string $idDestination) {
        $url = "/lists/{$idSource}/moveAllCards";
        $this->fetch($url);
    }

    private function moveCards(string $idSource, string $idDestination, array $cards) {
        $url = "/lists/{$idSource}/moveCards/cardsIDS";
        $this->fetch($url, ["destination" => $idDestination]);
    }

    private function getAllCardsInList(string $idList) {
        $url = "https://api.trello.com/1/lists/{$idList}/cards";
        $this->fetch($url);
    }

    private function getCardInList(string $idList, string $idCard) {
        $url = "https://api.trello.com/1/lists/{$idList}/cards/{$idCard}";
        $this->fetch($url);
    }

    private function getMembersId($idMember) {
        $url = "https://api.trello.com/1/members/{$idMember}";
        $this->fetch($url);
    }

    private function setMembersCard($idCard) {
        $url = "https://api.trello.com/1/cards/{$idCard}/idMembers";
        $this->fetch($url);
    }

    private function getAllMembersInBoard($idMember) {
        $url = "https://api.trello.com/1/boards/{$idMember}/members";
        $this->fetch($url);
    }

    private function getAllListInBoard($idBoard) {
        $url = "https://api.trello.com/1/boards/{$idBoard}/lists";
        $this->fetch($url);
    }

    private function getCardsInBoard($idBoard, $IdCard) {
        $url = "https://api.trello.com/1/boards/{$idBoard}/cards/{$idCard}";
        $this->fetch($url);
    }

    private function getAllCardsInBoard($idBoard) {
        $url = "https://api.trello.com/1/boards/{$idBoard}/cards";
        $this->fetch($url);
    }

    public function getAllProjectTasks() {
        return $this->getAllCardsInList("");
    }

    public function setSubjectTasks(array $tasks) {
        return $this->moveCards();
    }
}