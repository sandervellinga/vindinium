<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

define('DEBUG', true);

$loader = require __DIR__.'/vendor/autoload.php';

$size = 10;
$tiles = '
##@1    ####    @4##
      ########      
        ####        
    []        []    
$-    ##    ##    $-
$-    ##    ##    $-
    []        []    
        ####  @3    
      ########      
##@2    ####      ##';

//$tiles = preg_replace( "/\r|\n/", "", $tiles);
//$map = new \vindinium\World\Map($size, $tiles);
//
//$myLocation = '1;0';
//$someMine = $map->getMine();
//$path = $map->getPath($myLocation, $someMine);
//echo 'mylocation' . $myLocation . "\n";
//echo 'somemine' . $someMine . "\n";
//$here = array_shift($path);
//print_r($path);

use vindinium\Application;

$app = new Application();
$app->run();
