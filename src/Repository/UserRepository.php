<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class UserRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserByLogin(string $login): array
    {
        $sql = <<<SQL
SELECT 
    password
FROM
    users
WHERE
    login = :login
LIMIT 1
SQL;
        $user = $this->connection->fetchFirstColumn($sql, [
            'login' => $login
        ]);

        return $user;
    }

    public function isPasswordValid(string $login, string $password): bool
    {
        $sql = <<<SQL
SELECT 
    password
FROM
    users
WHERE
    login = :login
LIMIT 1
SQL;

        $hash = $this->connection->fetchFirstColumn($sql, [
            'login' => $login
        ]);

        return !(!$hash or !password_verify($password, $hash));
    }
}