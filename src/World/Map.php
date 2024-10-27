<?php
namespace vindinium\World;

use vindinium\Structure\GridWithWeights;
use vindinium\Structure\PriorityQueue;

class Map
{
    private $size;
    private $tiles;
    private $graph;

    private $heroes = [];
    private $taverns = [];
    private $tavernAccess = [];
    private $mines = [];
    private $mineAccess = [];

    public function __construct($size, $tiles)
    {
        $this->graph = new GridWithWeights($size, $size);

        $this->size = $size;
        $this->tiles = $tiles;

        $this->init();
    }

    public function getHeroLocation($hero)
    {
        $x = $this->heroes[$hero]->pos->x;
        $y = $this->heroes[$hero]->pos->y;

        return $this->graph->xyToId($y, $x); // WARNING!! json from server returns X as vertical and Y as horizontal
    }

    public function getHeroAttribute($hero, $attribute)
    {
        return $this->heroes[$hero]->$attribute;
    }

    public function getMineOwners()
    {
        return $this->mines;
    }
    public function getMineAccessLocations()
    {
        return $this->mineAccess;
    }

    public function getTavernAccessLocations()
    {
        return $this->tavernAccess;
    }

    public function getPath($start, $goal)
    {
        [$came_from, $cost_so_far] = $this->a_star_search($start, $goal);
        $path = $this->reconstruct_path($came_from, $start, $goal);

        if (DEBUG && $path == null) {
            echo 'No path found from ' . $start . ' to ' . $goal . "\n";
        }

        return $path;
    }

    public function getGraph(): GridWithWeights
    {
        return $this->graph;
    }

    public function update($state)
    {
        $this->updateHeroes($state->game->heroes);
        $this->updateMineOwners($state->game->board);
    }

    public function updateHeroes($heroes)
    {
        foreach ($heroes as $hero) {
            $this->heroes[$hero->id] = $hero;
        }
    }

    private function init()
    {
        $readPos = 0;
        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                $tile = mb_substr($this->tiles, $readPos, 2);
                echo $tile;

                $this->setTileProperties($this->graph->xyToId($x, $y), $tile);
                $readPos +=2;
            }
            echo "\n";
        }


        foreach ($this->mines as $mineId => $mine) {
            foreach ($this->graph->neighbors($mineId) as $accessLocation) {
                $this->mineAccess[$mineId][] = $accessLocation; // Also store mine id so we can indentify owner
            }
        }

        foreach ($this->taverns as $tavernId) {
            foreach ($this->graph->neighbors($tavernId) as $accessLocation) {
                $this->tavernAccess[$tavernId][] = $accessLocation;
            }
        }
    }

    /**
     * Update mine owners after each turn, this is the only info we cannot retrieve from json directly
     */
    private function updateMineOwners($board)
    {
        foreach ($this->mines as $id => $owner) {
            [$x, $y] = $this->graph->getXyFromId($id);
            $readPos = ($y*($this->size*2)) + ($x*2);
            $tile = mb_substr($board->tiles, $readPos, 2);
            $this->mines[$id] = $tile;
        }

    }

    private function setTileProperties($id, $tile)
    {
        switch ($tile) {
            // ## Impassable wood
            case '##':
                $this->graph->addWall($id);
                break;
            // @1 Hero number 1
            case '@1':
            case '@2':
            case '@3':
            case '@4':
//                $this->heroes[$tile] = $id;
                break;
            // [] Tavern
            case '[]':
                $this->graph->addWall($id);
                $this->taverns[] = $id;
                break;
            // $- Gold mine (neutral)
            // $1 Gold mine (belonging to hero 1)
            case '$-':
            case '$1':
            case '$2':
            case '$3':
            case '$4':
                $this->graph->addWall($id);
                $this->mines[$id] = $tile;
                break;
        }
    }

    private function reconstruct_path($came_from, $start, $goal)
    {
        $path = null;
        if (array_key_exists($goal, $came_from)) { // Make sure goal is reachable
            $current = $goal;
            $path[] = $current;
            while ($current != $start) {
                $current = $came_from[$current];
                $path[] = $current;
            }

            $path = array_reverse($path);
        }

        return $path;
    }

    private function heuristic($goal, $direction)
    {
        [$goalX, $goalY] = explode(';', $goal);
        [$directionX, $directionY] = explode(';', $direction);

        return abs($goalX - $directionX) + abs($goalY - $directionY);
    }

    private function a_star_search($start, $goal)
    {
        $frontier = new PriorityQueue();
        $frontier->put($start, 0);
        $came_from[$start] = null;
        $cost_so_far[$start] = 0;

        while (!$frontier->isEmpty()) {
            $current = $frontier->get();

            if ($current == $goal) {
                break; // we found destination so we can stop
            }

            foreach ($this->graph->neighbors($current) as $next) {
                $new_cost = $cost_so_far[$current] + $this->graph->cost($current, $next);
                if (!array_key_exists($next, $cost_so_far) || $new_cost < $cost_so_far[$next]) {
                    $cost_so_far[$next] = $new_cost;
                    $priority = $new_cost + $this->heuristic($goal, $next);
                    $frontier->put($next, $priority);
                    $came_from[$next] = $current;
                }
            }
        }

        return [$came_from, $cost_so_far];
    }
}