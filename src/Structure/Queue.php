<?php
namespace vindinium\Structure;

class Queue
{
    public $elements = [];

    public function isEmpty()
    {
        return empty($this->elements);
    }

    public function put($x)
    {
        array_push($this->elements, $x);
    }

    public function get()
    {
        return array_shift($this->elements);
    }
}