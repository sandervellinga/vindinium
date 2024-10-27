<?php
namespace vindinium\Structure;

class GridWithWeights extends SquareGrid
{
    private $costs = [];

    public function __construct($width, $height)
    {
        parent::__construct($width, $height);
    }

    public function cost($from_node, $to_node)
    {
        return 1;
    }
}