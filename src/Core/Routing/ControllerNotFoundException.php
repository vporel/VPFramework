<?php
namespace VPFramework\Core\Routing;

class ControllerNotFoundException extends \Exception
{
    private $controllerClass;
    public function __construct($controllerClass)
    {
        $this->controllerClass = $controllerClass;
        parent::__construct("Le controller $controllerClass est introuvable.");
    }

    public function getControllerClass(){ return $this->controllerClass; }
}