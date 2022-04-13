<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Route\Route;
use VPFramework\Core\Constants;

//Chargement des routes
class RouteConfiguration{

    /**
     * Tableau associatif dont les éléments ont pour clé le nom de la route et pour valeur la route correspondante
     */
    private $routes;

    public function getRoutes(): ?array
    {        
        if($this->routes == null){
            $routes = require Constants::$APP_ROOT."/Config/routes.php";
            if($routes === null || !is_array($routes)){
                throw new VPFrameworkConfigurationException("La valeur retournée par le fichier routes.php est invalide");
            }
            //Prise en compte des routes par défaut du framework (ex:/admin)
            $routes = array_merge($routes, require Constants::FRAMEWORK_ROOT."/DefaultApp/Config/routes.php");
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