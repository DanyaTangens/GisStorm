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

        return $this->makeEntity($rows);
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
    MBRContains(GeomFromText('Polygon(({$bounds->getMapBounds()}))'), point) AND 
    is_del != 1
SQL;

        $rows = $this->connection->fetchAllAssociative($sql);

        $data = [];

        foreach ($rows as $row) {
            $data[] = $this->makeEntity($row);
        }

        return $data;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function addCoupling(Coupling $couplingAdd): int
    {
        $sql = <<<SQL
INSERT INTO
	coupling 
SET 
    name = :name,
    type_coupling = :type_coupling,
    description = :description,
    point = GeomFromText('POINT({$couplingAdd->getLat()} {$couplingAdd->getLng()})', 0)
SQL;
        $this->connection->executeQuery($sql, [
            'name' => $couplingAdd->getName(),
            'type_coupling' => $couplingAdd->getTypeCoupling(),
            'description' => $couplingAdd->getDescription(),
        ]);

        return $this->connection->lastInsertId();
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

    public function deleteById($id)
    {
        $sql = <<<SQL
UPDATE 
	coupling 
SET 
    is_del = 1
WHERE 
    id = :id
SQL;
        $this->connection->executeQuery($sql, [
            'id' => $id
        ]);
    }

    public function editCoupling(Coupling $coupling)
    {
        $sql = <<<SQL
UPDATE 
	coupling 
SET
    point = GeomFromText('POINT({$coupling->getLat()} {$coupling->getLng()})', 0)
WHERE 
    id = :id
SQL;
        $this->connection->executeQuery($sql, [
            'id' => $coupling->getId()
        ]);
    }
}