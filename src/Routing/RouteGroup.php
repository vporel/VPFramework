<?php

namespace VPFramework\Routing;

use VPFramework\Core\DIC;

/**
 * Cette classe définit un groupe de routes
 * Les routes d'un groupe ont le même controller, et leur chemin commence par la même chaine
 */
class RouteGroup
{
    private $controllerClass;
    private $pathStart;
    /**
     * @var array
     */
    private $routes = [];
        
    /**
     * __construct
     * @param  string $controllerClass Nom de la classe sans le namespace (ex : HomeController)
     * @param  string $pathStart Début du chemin pour les routes du groupe
     * @param array $routesInGroup Un tableau des routes dans le groupe
     * @return void
     */
    public function __construct(string $controllerClass, string $pathStart, array $routesInGroup)
    {
        $this->controllerClass = $controllerClass;
        $this->pathStart = $pathStart;

        foreach($routesInGroup as $routeInGroup){
            $routePath = $pathStart.$routeInGroup->getPath();
            $this->routes[] = new Route($routeInGroup->getName(), $this->controllerClass, $routeInGroup->getControllerMethod(), $routePath, $routeInGroup->getRequiredParameters());
        }
    }



    /**
     * Get the value of pathStart
     */ 
    public function getPathStart()
    {
        return $this->pathStart;
    }

    /**
     * Get the value of controllerClass
     */ 
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * Get the value of routes
     *
     * @return  array
     */ 
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $namespace
     * 
     * @return RouteGroup
     */
    public function setControllerNamespace(string $namespace):RouteGroup
    {
        foreach($this->routes as $route){
            $route->setControllerNamespace($namespace);
        }
        return $this;
    }
}