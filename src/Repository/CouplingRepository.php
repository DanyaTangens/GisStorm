<?php

namespace App\Repository;

use App\Elements\Bounds;
use App\Elements\Coupling;
use Doctrine\DBAL\Connection;

class CouplingRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id)
    {
        $sql = <<<SQL
SELECT 
    id, 
    name, 
    type_coupling, 
    description	
FROM 
    coupling 
WHERE 
    id = :id
SQL;

        $data = $this->connection->fetchAllAssociative($sql, [
            'id' => $id
        ]);

        return $data;
    }

    public function getByBounds(Bounds $bounds)
    {

    }

    public function addCoupling(Coupling $couplingAdd)
    {
        $sql = <<<SQL
INSERT INTO
	coupling 
SET 
    name = :name,
    type_coupling = :type_coupling,
    description = :description,
    point = GeomFromText('POINT(:lat :lng)', 0)
SQL;
        $this->database->getConnection()->prepare($sql, [
            'name' => $couplingAdd->getName(),
            'type_coupling' => $couplingAdd->getTypeCoupling(),
            'description' => $couplingAdd->getDescription(),
            'lat' => $couplingAdd->getLat(),
            'lng' => $couplingAdd->getLng(),
        ])->execute();

    }
}