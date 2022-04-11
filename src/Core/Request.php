<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\RouteConfiguration;
use VPFramework\Core\Routing\Route;

/**
 * Classe qui modélise une requete et recoit dans son constructeur les parametres de cette requete
 */

class Request
{
    public const DEFAULT_ROUTE_NAME = "default-vpframework-route";
    private 
        $parameters,
        $route = null,
        $urlPath;

    /**
     * Constructeur
     * @param array $parameters parametres de la requete
     */
    public function __construct(RouteConfiguration $config)
    {
        $this->parameters = array_merge($_GET, $_POST);
        if(strpos($_SERVER["REQUEST_URI"], "?") > -1)
            $this->urlPath = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "?"));
        else
            $this->urlPath = $_SERVER["REQUEST_URI"];
        $routes = $config->getRoutes();
        if(count($routes) > 0){
            foreach($routes as $route){
                if(preg_match( $route->getPathRegex(), $this->urlPath, $matches)){
                    $this->route = $route;
                    break;
                }
            }
            if($this->route == null)
                throw new \Exception("URL $this->urlPath non reconnue");
            
            //array_slice($matches, 1) car le premier resultat dans matches est la chaine complete
            foreach($this->route->getData(array_slice($matches, 1)) as $key => $value)
                $this->set($key, $value);
        }else{

            //Route par défaut définie par le framework
            $this->route = new Route(self::DEFAULT_ROUTE_NAME, "", "", "");
        }

    }

    /**
     * Renvoie la valeur du parametre demandé
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if(isset($this->parameters[$name]))
        {
            return $this->parameters[$name];
        }
        return null;
    }

    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Renvoie tous les parametres de la requête
     * @return array
     */
    public function getAll(): ?array
    {
        return $this->parameters;   
    }
    
    public function getUrlPath(): string
    {
        return $this->urlPath;
    }

    public function getRoute(): Route 
    {
        return $this->route;
    }
}
