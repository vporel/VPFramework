<?php

namespace VPFramework\Core\Routing;

use VPFramework\Core\Constants;
use VPFramework\Routing\RouteGroup as RoutingRouteGroup;

/**
 * This class is a subclass of the RouteGroup class in Routing component 
 * It just make the constructor easy to call
 * The routes in the same group have the same controller, and their paths begin by the same string
 */
class RouteGroup extends RoutingRouteGroup
{
        
    /**
     * __construct
     * @param  string $controllerClass Nom de la classe sans le namespace (ex : HomeController)
     * @param  string $pathStart Début du chemin pour les routes du groupe
     * @param array $routesInGroup Un tableau des routes dans le groupe
     * @return void
     */
    public function __construct(string $controllerClass, string $pathStart, array $routesInGroup)
    {
        parent::__construct(Constants::CONTROLLER_NAMESPACE."\\".$controllerClass, $pathStart, $routesInGroup);
    }
}