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

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getById(int $id): Coupling
    {
        $sql = <<<SQL
SELECT 
    id, 
    name, 
    type_coupling, 
    description,
    X(point) as x,
    Y(point) as y
FROM 
    coupling 
WHERE 
    id = :id
SQL;

        $rows = $this->connection->fetchAssociative($sql, [
            'id' => $id
        ]);

        return $this->makeEntity($rows[0]);
    }

    /**
     * @return Coupling[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByBounds(Bounds $bounds): array
    {
        $sql = <<<SQL
SELECT 
    id,
    name,
    type_coupling,
    description,
    X(point) as x,
    Y(point) as y
FROM
     coupling
WHERE
    MBRContains(GeomFromText('Polygon(({$bounds->getMapBounds()}))'), point)
SQL;

        $rows = $this->connection->fetchAllAssociative($sql);

        $data = [];

        foreach ($rows as $row) {
            $data[] = $this->makeEntity($row);
        }

        return $data;
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

    private function makeEntity(array $data)
    {
        return new Coupling(
            $data['id'],
            $data['name'],
            $data['type_coupling'],
            $data['description'],
            $data['x'],
            $data['y'],
        );
    }
}