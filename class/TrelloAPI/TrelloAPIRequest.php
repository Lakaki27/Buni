<?php

namespace Buni\TrelloAPI;

use ReturnTypeWillChange;
use Symfony\Component\HttpClient\HttpClient;

class TrelloAPIRequest
{
    const API_URL = "https://api.trello.com/1";
    const API_TOKEN = "ATTAaa642b4e0254d2cc4a651e5f032e359751d0f2ee285c8836d85f58e4604929eb1253D5EA";
    const API_KEY = "7399da477dccc44d309c3b0d4a0e66b1";

    private $apiClient;

    private static ?TrelloAPIRequest $_instance = null;

    private function __construct()
    {
        $this->apiClient = HttpClient::create();
    }

    public static function getInstance(): TrelloAPIRequest
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function fetch($method, $url, $params = [])
    {
        $response = null;
        if ($params !== []) {
            $response = $this->apiClient->request(
                $method,
                self::API_URL . $url,
                [
                    "query" => [
                        "key" => self::API_KEY,
                        "token" => self::API_TOKEN,
                        ...$params
                    ]
                ]
            );
        } else {
            $response = $this->apiClient->request(
                $method,
                self::API_URL . $url,
                [
                    "query" => [
                        "key" => self::API_KEY,
                        "token" => self::API_TOKEN
                    ]
                ]
            );
        }

        return $response->toArray();
    }

    public function getAllCards($boardId)
    {
        $method = 'GET';

        $url = "/boards/{$boardId}/cards";

        $params = [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];

        $cards = $this->fetch($method, $url);
        $cardsData = [];

        foreach ($cards as $card) {
            $cardsData[] = [
                "id" => $card['id'],
                "name" => $card['name'],
                "listId" => $card['idList']
            ];
        }

        return $cardsData;
    }

    public function addMemberToCard($cardId, $memberId)
    {
        $method = 'PUT';

        $url = "/cards/{$cardId}";

        $params = [
            "idMembers" => $memberId
        ];

        return $this->fetch($method, $url, $params);
    }

    public function getAllLists($boardId)
    {
        $method = 'GET';

        $url = "/boards/{$boardId}/lists";

        $lists = $this->fetch($method, $url);
        $listsData = [];

        foreach ($lists as $list) {
            $listsData[$list['name']] = $list['id'];
        }

        return $listsData;
    }

    public function moveAllCards($sourceListId, $destinationListId, $boardId)
    {
        $method = 'POST';

        $url = "/lists/{$sourceListId}/moveAllCards";

        $params = [
            "idList" => $destinationListId,
            "idBoard" => $boardId
        ];

        return $this->fetch($method, $url, $params);
    }
}
