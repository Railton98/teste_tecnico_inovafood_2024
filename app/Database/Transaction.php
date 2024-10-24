<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Transaction
{
    private static ?PDO $conn = null;

    public static function open(): void
    {
        self::$conn = Connection::connect();
        self::$conn->beginTransaction();
    }

    public static function getConnection(): ?PDO
    {
        return self::$conn;
    }

    public static function rollback(): void
    {
        if (self::$conn) {
            self::$conn->rollBack();
            self::$conn = null;
        }
    }

    public static function close(): void
    {
        if (self::$conn) {
            self::$conn->commit();
            self::$conn = null;
        }
    }
}
