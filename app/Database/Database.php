<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Database
{
    public function __construct(
        protected PDO $connection,
        protected string $table
    ) {
    }

    public function insert(array $data): int
    {
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $binds = implode(', :', $keys);

        $sql = "INSERT INTO $this->table($columns) VALUES(:$binds)";
        $stmt = $this->connection->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(
                param: ":$key",
                value: $value,
                type: is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }

        $stmt->execute();

        return (int) $this->connection->lastInsertId();
    }
}
