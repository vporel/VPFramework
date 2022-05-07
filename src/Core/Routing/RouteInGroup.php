<?php

namespace VPFramework\Core\Routing;

/**
 * Cette classe définit une route dans lun groupe
 * Aucun traitement n'est fait, elle permet juste de récupérer des informations
 */
class RouteInGroup
{
    private $name, $path, $controllerMethod;
        
    /**
     * __construct
     * @param  string $name
     * @param  string $controllerMethod
     * @param  string $path
     * @return void
     */
    public function __construct(string $name, string $controllerMethod, string $path)
    {
        $this->name = $name;
        $this->path = $path;
        $this->controllerMethod = $controllerMethod;
    }

    public function getName(){
        return $this->name;
    }

    public function getControllerMethod(){
        return $this->controllerMethod;
    }

    public function getPath(){
        return $this->path;
    }
}