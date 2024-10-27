<?php
namespace vindinium\Structure;

class PriorityQueue
{
    public $elements = [];

    public function isEmpty()
    {
        return empty($this->elements);
    }

    public function put($x, $priority)
    {
        if (empty($this->elements) || $priority >= $this->elements[count($this->elements)-1]['priority']) {
            $this->elements[] = ['id' => $x, 'priority' => $priority];
        } else {
            foreach ($this->elements as $index => $element) {
                if ($priority < $element['priority']) {
                    $insert = $index;
                    break;
                }
            }

            $this->elements = array_merge(array_slice($this->elements, 0, $insert), [['id' => $x, 'priority' => $priority]], array_slice($this->elements, $insert));
        }
    }

    public function get()
    {
        return array_shift($this->elements)['id'];
    }
}