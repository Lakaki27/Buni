<?php

namespace Buni\TrelloAPI;

use Symfony\Component\HttpClient\HttpClient;

class TrelloAPIRequest {
    const API_URL = "https://api.trello.com";
    const API_TOKEN = "7399da477dccc44d309c3b0d4a0e66b1";
    const API_KEY = "85d25e95687a41f3399b24b9285ceeef18694db8646db9d12e9e322813096fb1";

    private $apiClient;

    public function __construct()
    {
        $this->apiClient = HttpClient::create();
    }
}