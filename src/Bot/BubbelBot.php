<?php
namespace vindinium\Bot;

use vindinium\World\Map;

class BubbelBot extends Bot
{
    private $map;
    private $myId;
    private $myLife = 100;
    private $currentPath = [];
    private $currentGoal; // This is the id for a mine or tavern
    private $currentGoalType;

    public function __construct($state)
    {
        $size = $state->game->board->size;
        $tiles = $state->game->board->tiles;
        $this->map = new Map($size, $tiles);

        // Set our own hero id
        foreach ($state->game->heroes as $hero) {
            if ($hero->name == 'bubbelbot') {
                $this->myId = $hero->id;
                break;
            }
        }

        $this->map->updateHeroes($state->game->heroes);
        $this->map->getHeroLocation($this->myId);

    }

    public function move($state)
    {
        $this->map->update($state);
        $this->myLife = $this->map->getHeroAttribute($this->myId, 'life');

        $direction = 'Stay';
        $currentLocation = $this->map->getHeroLocation($this->myId);
        if (empty($this->currentPath) || $currentLocation !== $this->currentPath[0]) {
            if ($this->myLife > 50) {
                $this->setPathToClosestMine($currentLocation);
            } else {
                // Would like some beer
                $this->setPathToClosestTavern($currentLocation);
            }
        } else if ($this->myLife < 20) {
            //  COULD REALLY USE SOME BEER!!111
            $this->setPathToClosestTavern($currentLocation);
        }

        echo $this->currentGoalType . ' ' . $this->currentGoal . ' | ';

        if (count($this->currentPath) > 1) { // Destination location will be only remaining location in path
            $currentLocation = array_shift($this->currentPath);
            $nextLocation = $this->currentPath[0];

            $direction = $this->calculateDirection($currentLocation, $nextLocation);
        } else if (count($this->currentPath) === 1) {
            $currentLocation = $this->currentPath[0];
            $direction = $this->calculateDirection($currentLocation, $this->currentGoal);

            if ($this->myLife == 99 || $this->currentGoalType == 'Mine') {
                $this->currentGoal = null;
                $this->currentGoalType = null;
                $this->currentPath = [];
            }

        }

        echo $direction . ' (hp: ' . $this->myLife . ")\n";

        return $direction;
    }

    private function setPathToClosestMine($currentLocation)
    {
        // Reset goal and path
        $this->currentGoal = null;
        $this->currentGoalType = 'Mine';
        $this->currentPath = [];

        $mineAccessLocations = $this->map->getMineAccessLocations();
        $mineOwners = $this->map->getMineOwners();
        foreach ($mineOwners as $mineId => $owner) {
            if ($owner != '$'.$this->myId) { // Skip mines we already own
                foreach ($mineAccessLocations[$mineId] as $location) {
                    $path = $this->map->getPath($currentLocation, $location);

                    if (empty($this->currentPath) || count($path) < count($this->currentPath)) {
                        $this->currentGoal = $mineId;
                        $this->currentGoalType = 'Mine';
                        $this->currentPath = $path;
                    }
                }
            }
        }
    }

    private function setPathToClosestTavern($currentLocation)
    {
        // Reset goal and path
        $this->currentGoal = null;
        $this->currentGoalType = null;
        $this->currentPath = [];

        $tavernAccessLocations = $this->map->getTavernAccessLocations();
        foreach ($tavernAccessLocations as $tavernId => $locations) {
            foreach ($locations as $location) {
                $path = $this->map->getPath($currentLocation, $location);

                if (empty($this->currentPath) || count($path) < count($this->currentPath)) {
                    $this->currentGoal = $tavernId;
                    $this->currentGoalType = 'Tavern';
                    $this->currentPath = $path;
                }
            }
        }
    }

    private function calculateDirection($currentLocation, $nextLocation)
    {
        $direction = 'Stay';

        [$currentX, $currentY] = $this->map->getGraph()->getXyFromId($currentLocation);
        [$nextX, $nextY] = $this->map->getGraph()->getXyFromId($nextLocation);

        $directionX = $nextX - $currentX;
        $directionY = $nextY - $currentY;


        if ($directionY === -1) {
            $direction = 'North';
        } else if ($directionY === 1) {
            $direction = 'South';
        } else if ($directionX === -1) {
            $direction = 'West';
        } else if ($directionX === 1) {
            $direction = 'East';
        }

        return $direction;
    }
}