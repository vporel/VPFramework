<?php

namespace VPFramework\Core\Routing;

use InvalidArgumentException;
use VPFramework\Core\Constants;
use VPFramework\Core\DIC;
use VPFramework\Routing\Route as RoutingRoute;

/**
 * This class is a subclass of the Route class in Routing component 
 * It just make the constructor easy to call
 */
class Route extends RoutingRoute
{
        
    /**
     * __construct
     * @param  string $name
     * @param  string $controllerAndMethod Ex : HomeController:index (Controller without the namespace)
     * @param  string $path
     * @param  array $requiredParameters
     * @return void
     */
    public function __construct(string $name, string $controllerAndMethod, string $path, array $requiredParameters = [])
    {
        $explodeControllerAndMethod = explode(":", $controllerAndMethod);
        if(count($explodeControllerAndMethod) == 2){
            $controllerClass = Constants::CONTROLLER_NAMESPACE."\\".$explodeControllerAndMethod[0];
            $controllerMethod = $explodeControllerAndMethod[1];
        }else{
            throw new InvalidArgumentException("Le parametre controllerAndMethod doit respecter le pattern [controller:method]");
        }
        parent::__construct($name, $controllerClass, $controllerMethod, $path, $requiredParameters);
    }

}