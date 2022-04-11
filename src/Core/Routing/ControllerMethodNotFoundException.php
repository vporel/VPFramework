<?php
namespace VPFramework\Core\Routing;

class ControllerMethodNotFoundException extends \Exception
{
    public function __construct($controllerClass, $method)
    {
        parent::__construct("Le controller $controllerClass ne contient pas de méthode $method");
    }

}