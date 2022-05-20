<?php

namespace VPFramework\Core\Configuration;

use InvalidArgumentException;
use VPFramework\Core\Constants;
use VPFramework\Routing\Loader;
use VPFramework\Routing\RouteGroup;
use VPFramework\Utils\PHPFile;

//Chargement des routes
class RouteConfiguration{

    /**
     * Tableau associatif dont les éléments ont pour clé le nom de la route et pour valeur la route correspondante
     */
    private $routes;

    public function getRoutes(): ?array
    {        
        if($this->routes == null){
            try{
                $routes = Loader::fromFile(Constants::PROJECT_ROOT."/Config/routes.php");
                //Prise en compte des routes par défaut du framework (ex:/admin)
                $routes = array_merge(Loader::fromFile(Constants::FRAMEWORK_ROOT."/InternalApp/Config/routes.php"), $routes);
            }catch(InvalidArgumentException $e){
                throw new ConfigurationException("Le fichier routes.php doit retoruner un tableau");
            }
            $controllers = [];
            foreach(scandir(Constants::CONTROLLER_DIR) as $file){
                if(!is_dir(Constants::CONTROLLER_DIR."/".$file)){
                    try{
                        $phpFile = new PHPFile(Constants::CONTROLLER_DIR."/".$file);
                    }catch(InvalidArgumentException $e){
                        continue;
                    }
                    if($class = $phpFile->findClass()){
                        $routes = array_merge($routes, Loader::fromClass($class));
                    }
                }
            }
            //Recherche des routes dans les controllers
            $this->routes = $routes;
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