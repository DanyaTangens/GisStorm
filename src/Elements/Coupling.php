<?php

namespace App\Elements;

class Coupling
{
    private int $id;
    private string $name;
    private int $typeCoupling;
    private string $description;
    private float $lat;
    private float $lng;

    public function __construct(int $id, string $name, int $typeCoupling, string $description, float $lat, float $lng)
    {
        $this->id = $id;
        $this->name = $name;
        $this->typeCoupling = $typeCoupling;
        $this->description = $description;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeCoupling(): int
    {
        return $this->typeCoupling;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

}