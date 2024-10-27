<?php
namespace vindinium\Structure;

class SquareGrid
{
    public $width;
    public $height;
    public $walls = [];

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function addWall($id)
    {
        $this->walls[] = $id;
    }

    public function in_bounds($id)
    {
        [$x, $y] = $this->getXyFromId($id);
        return 0 <= $x && $x < $this->width && 0 <= $y && $y < $this->height;
    }

    public function passable($id)
    {
        return !in_array($id, $this->walls);
    }

    public function neighbors($id)
    {
        $neighbors = [];
        [$x, $y] = $this->getXyFromId($id);

        $north = $this->xyToId($x, $y-1);
        $east = $this->xyToId($x+1, $y);
        $south = $this->xyToId($x, $y+1);
        $west = $this->xyToId($x-1, $y);
        $results = [$east, $north, $west, $south];

        foreach ($results as $result) {
            if ($this->in_bounds($result) && $this->passable($result)) {
                $neighbors[] = $result;
            }
        }

        return $neighbors;
    }

    public function xyToId($x, $y)
    {
        return $x . ';' . $y;
    }

    public function getXyFromId($id)
    {
        return explode(';', $id);
    }
}