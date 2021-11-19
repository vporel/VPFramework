<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Route\Route;

const APP_FILE = __DIR__."/../../../../../../config/app.json";
const ROUTES_FILE = __DIR__."/../../../../../../config/routes.json";
const SERVICES_FILE = __DIR__."/../../../../../../config/services.json";

class Configuration
{
    
    private 
        $routes = null;

    private
        $app = null,
        $services = null;

    /**
     * The data returned by this functions are from the app.json file
     */
    public function get($name){
        if($this->app === null)
            $this->app = json_decode(file_get_contents(APP_FILE), true);
        if(array_key_exists($name, $this->app))
            return $this->app[$name];
        else
            throw new \Exception("L'élément $name n'a pas été trouvé dans la configuration de l'application");
    }

    public function getService($name){
        if($this->services === null){
            $this->services = json_decode(file_get_contents(SERVICES_FILE), true);
        }
        if(array_key_exists($name, $this->services))
            return $this->services[$name];
        else 
            throw new ServiceNotFoundException($name);
    }

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
    
}