<?php

namespace VPFramework\Routing;

/**
 * Cette classe définit une route dans lun groupe
 * Aucun traitement n'est fait, elle permet juste de récupérer des informations
 */
class RouteInGroup
{
    private $name, $path, $controllerMethod, $requiredParameters;
        
    /**
     * __construct
     * @param  string $name
     * @param  string $controllerMethod
     * @param  string $path
     * @param  array $requiredParameters
     * @return void
     */
    public function __construct(string $name, string $controllerMethod, string $path, array $requiredParameters = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->controllerMethod = $controllerMethod;
        $this->requiredParameters = $requiredParameters;
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

    /**
     * Get the value of requiredParameters
     */ 
    public function getRequiredParameters()
    {
        return $this->requiredParameters;
    }
}