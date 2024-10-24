<?php

namespace Buni\Views;

use Buni\Database\Connection;

class View
{
    private string $url;
    private Connection $appDb;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->appDb = Connection::getInstance();
    }

    private function matchUrl(): string|bool
    {
        $urlToMatch = explode('?', $this->url)[0];
        $stmt = $this->appDb->select(
            "SELECT * FROM views WHERE request_path LIKE :path",
            ["path" => $urlToMatch]
        );

        if (count($stmt) > 0) {
            return $stmt[0]["real_path"];
        } else {
            return false;
        }
    }

    public function getViewURL(): string
    {
        $realUrl = "";
        if ($this->url === "" || $this->url === "/") {
            return "/home/views/home.php";
        } else {
            $realUrl = $this->matchUrl();

            if ($realUrl) {
                return $realUrl;
            } else {
                return "/security/views/404.php";
            }
        }
    }
}
