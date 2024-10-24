<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function connect(): ?PDO
    {
        $dbConnection = $_ENV['DB_CONNECTION'];
        $dbDatabase = $_ENV['DB_DATABASE'];
        $dbHost = $_ENV['DB_HOST'];
        $dbUser = $_ENV['DB_USERNAME'];
        $dbPassword = $_ENV['DB_PASSWORD'];

        if (!self::$connection) {
            try {
                self::$connection = new PDO(
                    dsn: "$dbConnection:host=$dbHost;dbname=$dbDatabase",
                    username: $dbUser,
                    password: $dbPassword,
                    options: [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
                );
            } catch (PDOException $e) {
                dump($e->getMessage());
            }
        }

        return self::$connection;
    }
}
