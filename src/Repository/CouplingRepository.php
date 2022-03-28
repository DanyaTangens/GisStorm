<?php

namespace App\Repository;

use App\Database;
use PDO;

class CouplingRepository
{
    private Database $connection;

    public function __construct(Database $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id)
    {
        $data = $this->connection->getConnection()->prepare(
            'SELECT * FROM hotelbase_city WHERE id = :id',
            [
                ':id' => $id
            ]
        )->fetchAll();

        return $data;
    }
}