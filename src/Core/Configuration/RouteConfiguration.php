<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Route\Route;

//Chargement des routes
class RouteConfiguration{

    /**
     * Tableau associatif dont les éléments ont pour clé le nom de la route et pour valeur la route correspondante
     */
    private $routes;

    public function getRoutes(): ?array
    {        
        if($this->routes == null){
            $routes = require ROOT."/config/routes.php";
            if($routes === null || !is_array($routes)){
                throw new VPFrameworkConfigurationException("La valeur retournée par le fichier routes.php est invalide");
            }
            $this->routes = [];
            foreach($routes as $route){
                $this->routes[$route->getName()] = $route;
            }
        }
        return $this->routes;
    }

    public function getRoute(string $name){
        $routes = $this->getRoutes();
        if(array_key_exists($name, $routes))
            return $routes[$name];
        else    
            throw new RouteNotFoundException($name);
    }
}