<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Route\Route;

const ROUTES_FILE = ROOT."/config/routes.json";
class RouteConfiguration{
    private $routes = null;


    public function getRoutes(): ?array
    {
        if($this->routes == null){
            $this->routesArray = json_decode(file_get_contents(ROUTES_FILE), true);
            foreach($this->routesArray as $name => $content){
                $this->routes[$name] = new Route($content);
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