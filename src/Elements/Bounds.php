<?php

namespace App\Elements;

class Bounds
{
    private const BORDER = 0.005;
    private float $swLat;
    private float $swLng;
    private float $neLat;
    private float $neLng;

    /**
     * @param float $swLat
     * @param float $swLng
     * @param float $neLat
     * @param float $neLng
     */
    public function __construct(float $swLat, float $swLng, float $neLat, float $neLng)
    {
        $this->swLat = $swLat;
        $this->swLng = $swLng;
        $this->neLat = $neLat;
        $this->neLng = $neLng;
    }

    public function getMapBounds(): string
    {
        $border = 0.005;
        $this->swLat = $this->swLat - $border;
        $this->swLng = $this->swLng - $border;
        $this->neLat = $this->neLat + $border;
        $this->neLng = $this->neLng + $border;

        $coords = '';

        $coords = $this->swLat . ' ' . $this->swLng . ',';
        $coords .= $this->neLat . ' ' . $this->swLng . ',';
        $coords .= $this->neLat . ' ' . $this->neLng . ',';
        $coords .= $this->swLat . ' ' . $this->neLng . ',';
        $coords .= $this->swLat . ' ' . $this->swLng;

        return $coords;
    }
}