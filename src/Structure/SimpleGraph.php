<?php
namespace vindinium\Structure;

class SimpleGraph
{
    public $edges = [];

    public function neighbors($id)
    {
        return $this->edges[$id];
    }
}