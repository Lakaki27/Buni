<?php

namespace Buni\Database;

use PDO;
use PDOException;

class Connection
{
    const DEFAULT_SQL_USER = "user";
    const DEFAULT_SQL_PASSWORD = "userpassword";
    const DEFAULT_SQL_HOST = "localhost:3306";
    const DEFAULT_SQL_DATABASE = "buni";
    const DEFAULT_SQL_CHARSET = "utf-8";

    private static ?Connection $_instance = null;

    private $pdo;

    private function __construct()
    {
        $dsn = "mysql:host=" . self::DEFAULT_SQL_HOST . ";dbname=" . self::DEFAULT_SQL_DATABASE . ";charset=" . self::DEFAULT_SQL_CHARSET;

        try {
            $this->pdo = new PDO($dsn, self::DEFAULT_SQL_USER, self::DEFAULT_SQL_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données. Veuillez réessayer.");
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
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function select($sql, $params = [])
    {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function insert($table, $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        $this->query($sql, $data);
    }

    public function update($table, $data, $where)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(', ', $set);
        $sql = "UPDATE $table SET $set WHERE $where";
        $this->query($sql, $data);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql);
    }
}
