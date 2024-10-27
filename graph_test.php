<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__.'/vendor/autoload.php';




use vindinium\Structure\SimpleGraph;
use vindinium\Structure\SquareGrid;
use vindinium\Structure\Queue;
use vindinium\Structure\PriorityQueue;
use vindinium\Structure\GridWithWeights;

$example_graph = new SimpleGraph();
$example_graph->edges = [
'A' => ['B'],
'B' => ['A', 'C', 'D'],
'C' => ['A'],
'D' => ['E', 'A'],
'E' => ['B']
];

function breadth_first_search_1($graph, $start)
{
# print out what we find
$frontier = new Queue();
$frontier->put($start);
$visited[$start] = true;

while (!$frontier->isEmpty()) {
$current = $frontier->get();
echo "Visiting " . $current . "\n";
foreach ($graph->neighbors($current) as $next) {
if (!array_key_exists($next, $visited)) {
$frontier->put($next);
$visited[$next] = true;
}
}
}
}
//breadth_first_search_1($example_graph, 'A');



function breadth_first_search_2($graph, $start)
{
# return came_from
$frontier = new Queue();
$frontier->put($start);
$came_from[$start] = null;

while (!$frontier->isEmpty()) {
$current = $frontier->get();
echo "Visiting " . $current . "\n";
foreach ($graph->neighbors($current) as $next) {
if (!array_key_exists($next, $came_from)) {
$frontier->put($next);
$came_from[$next] = $current;
}
}
}

return $came_from;
}

//$g = new SquareGrid(3, 3);
//$g->walls = ['1;0', '1;1'];
//$parents = breadth_first_search_2($g, '0;0');
//print_r($parents);



function breadth_first_search_3($graph, $start, $goal)
{
# return came_from
$frontier = new Queue();
$frontier->put($start);
$came_from[$start] = null;

while (!$frontier->isEmpty()) {
$current = $frontier->get();

if ($current == $goal) {
break; // we found destination so we can stop
}

echo "Visiting " . $current . "\n";
foreach ($graph->neighbors($current) as $next) {
if (!array_key_exists($next, $came_from)) {
$frontier->put($next);
$came_from[$next] = $current;
}
}
}

return $came_from;
}
//$g = new SquareGrid(3, 3);
//$g->walls = ['1;0', '1;1'];
//$parents = breadth_first_search_3($g, '0;0', '1;2');
//print_r($parents);




function dijkstra_search($graph, $start, $goal)
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

echo "Visiting " . $current . "\n";
foreach ($graph->neighbors($current) as $next) {
$new_cost = $cost_so_far[$current] + $graph->cost($current, $next);
if (!array_key_exists($next, $cost_so_far) || $new_cost < $cost_so_far[$next]) {
$cost_so_far[$next] = $new_cost;
$priority = $new_cost;
$frontier->put($next, $priority);
$came_from[$next] = $current;
}
}
}

return [$came_from, $cost_so_far];
}

//$start = '0;0';
//$goal = '2;2';
//$g = new GridWithWeights(3, 3);
//$g->walls = ['1;0', '1;1'];
//[$came_from, $cost_so_far] = dijkstra_search($g, $start, $goal);
//print_r($came_from);
//print_r($cost_so_far);
//$path = reconstruct_path($came_from, $start, $goal);
//print_r($path);

function reconstruct_path($came_from, $start, $goal)
{
$current = $goal;
$path[] = $current;
while ($current != $start) {
$current = $came_from[$current];
$path[] = $current;
}

return array_reverse($path);
}

function heuristic($goal, $direction)
{
[$goalX, $goalY] = explode(';', $goal);
[$directionX, $directionY] = explode(';', $direction);

return abs($goalX - $directionX) + abs($goalY - $directionY);
}

function a_star_search($graph, $start, $goal)
{
$frontier = new PriorityQueue();
$frontier->put($start, 0);
$came_from[$start] = null;
$cost_so_far[$start] = 0;
$count = 0;

while (!$frontier->isEmpty()) {
$current = $frontier->get();
$count++;
echo "Visiting {$count} " . $current . "\n";

if ($current == $goal) {
break; // we found destination so we can stop
}

foreach ($graph->neighbors($current) as $next) {
$new_cost = $cost_so_far[$current] + $graph->cost($current, $next);
if (!array_key_exists($next, $cost_so_far) || $new_cost < $cost_so_far[$next]) {
$cost_so_far[$next] = $new_cost;
$priority = $new_cost + heuristic($goal, $next);
$frontier->put($next, $priority);
$came_from[$next] = $current;
}
}
}

return [$came_from, $cost_so_far];
}

$start = '0;12';
$goal = '14;2';
$g = new GridWithWeights(15, 15);
$g->walls = ['2;2', '3;2', '4;2', '5;2', '6;2', '7;2', '8;2', '9;2', '10;2', '11;2', '12;2',
'2;12', '3;12', '4;12', '5;12', '6;12', '7;12', '8;12', '9;12', '10;12', '11;12', '12;12',
'12;3', '12;4', '12;5', '12;6', '12;7', '12;8', '12;9', '12;10', '12;11','1;2'

];


//$start = '1;1';
//$goal = '4;4';
//$g = new GridWithWeights(5, 5);

[$came_from, $cost_so_far] = a_star_search($g, $start, $goal);
print_r($came_from);
print_r($cost_so_far);
$path = reconstruct_path($came_from, $start, $goal);
print_r($path);


