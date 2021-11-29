<?php
namespace VPFramework\Core\Route;

class ControllerFileNotFoundException extends \Exception
{
    private $controllerClass;
    public function __construct($controllerClass)
    {
        $this->controllerClass = $controllerClass;
        parent::__construct("Le fichier du controller $controllerClass est introuvable.");
    }

    public function getControllerClass(){ return $this->controllerClass; }
}