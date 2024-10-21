<?php

namespace Buni\Database;

use PDO;
use PDOException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class Connection
{
    const DEFAULT_SQL_USER = "root";
    const DEFAULT_SQL_PASSWORD = "rootpassword";
    const DEFAULT_SQL_HOST = "mysql-container:3306";
    const DEFAULT_SQL_DATABASE = "buni";

    private static ?Connection $_instance = null;

    private $pdo;

    private function __construct()
    {
        $dsn =
            "mysql:host=" .
            self::DEFAULT_SQL_HOST .
            ";dbname=" .
            self::DEFAULT_SQL_DATABASE;

        try {
            $this->pdo = new PDO(
                $dsn,
                self::DEFAULT_SQL_USER,
                self::DEFAULT_SQL_PASSWORD
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($e);
        }
    }

    public static function getInstance(): Connection
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $th) {
            throw $th;
        }
    }

    public function select($sql, $params = [])
    {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw $th;
        }
    }

    public function insert($table, $data)
    {
        $keys = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        $this->query($sql, $data);
    }

    public function update($table, $data, $where)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(", ", $set);
        $sql = "UPDATE $table SET $set WHERE $where";
        $this->query($sql, $data);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql);
    }
}
