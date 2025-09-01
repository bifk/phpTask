<?php

require_once "EnvLoader.php";

class Database {
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;
    private $connection;

    public function __construct() {
        $envLoader = new EnvLoader();

        $this->host = $envLoader->getEnv("DB_HOST");
        $this->port = $envLoader->getEnv("DB_PORT");
        $this->database = $envLoader->getEnv("DB_NAME");
        $this->username = $envLoader->getEnv("DB_USER");
        $this->password = $envLoader->getEnv("DB_PASSWORD");
    }

    public function getConnection() {
        try {
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->database";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Ошибка подключения к базе данных: " . $e->getMessage());
            throw new PDOException($e->getMessage());
        }
        return $this->connection;
    }
}