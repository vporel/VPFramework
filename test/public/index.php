<?php 
    namespace App;

use VPFramework\Core\DIC;
use VPFramework\Core\Router;

//Autoloader de composer
require_once __DIR__."/../vendor/autoload.php";

define("ROOT", __DIR__."/..");

$DIC = DIC::getInstance();
$router = $DIC->get(Router::class);
$DIC->invoke($router, "end");

