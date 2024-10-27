<?php
namespace vindinium\World;

class Tile
{
    private $x;
    private $y;
    private $name = 'defaultname';

    private $northTile;
    private $eastTile;
    private $southTile;
    private $westTile;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setNorthTile(Tile $tile): void
    {
        $this->northTile = $tile;
    }

    public function getNorthTile(): ?Tile
    {
        $tile = null;
        if (!empty($this->northTile)) {
            $tile = $this->northTile;
        }

        return $tile;
    }

    public function setEastTile(Tile $tile): void
    {
        $this->eastTile = $tile;
    }

    public function getEastTile(): ?Tile
    {
        $tile = null;
        if (!empty($this->eastTile)) {
            $tile = $this->eastTile;
        }

        return $tile;
    }

    public function setSouthTile(Tile $tile): void
    {
        $this->southTile = $tile;
    }

    public function getSouthTile(): ?Tile
    {
        $tile = null;
        if (!empty($this->southTile)) {
            $tile = $this->southTile;
        }

        return $tile;
    }

    public function setWestTile(Tile $tile): void
    {
        $this->westTile = $tile;
    }

    public function getWestTile(): Tile
    {
        $tile = null;
        if (!empty($this->westTile)) {
            $tile = $this->westTile;
        }

        return $tile;
    }
}