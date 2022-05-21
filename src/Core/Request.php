<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\RouteConfiguration;
use VPFramework\Routing\Route;

/**
 * Classe qui modélise une requete et recoit dans son constructeur les parametres de cette requete
 */

class Request
{
    private 
        $parameters,
        $route = null,
        $routeConfig = null,
        $urlPath;

    /**
     * Constructeur
     * @param array $parameters parametres de la requete
     */
    public function __construct(RouteConfiguration $routeConfig)
    {
        $this->parameters = array_merge($_GET, $_POST);
        if(strpos($_SERVER["REQUEST_URI"], "?") > -1)
            $this->urlPath = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "?"));
        else
            $this->urlPath = $_SERVER["REQUEST_URI"];
        $this->routeConfig = $routeConfig;
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
        if($this->route == null){
            $routes = $this->routeConfig->getRoutes();
            if(count($routes) > 0){
                foreach($routes as $route){
                    if(preg_match( $route->getPathRegex(), $this->urlPath, $matches)){
                        $this->route = $route;
                        break;
                    }
                }
                if($this->route == null)
                    throw new \Exception("URL $this->urlPath non reconnue");
                //Récupération des paramètres de la route
                //array_slice($matches, 1) car le premier resultat dans matches est la chaine complete
                foreach($this->route->getData(array_slice($matches, 1)) as $key => $value)
                    $this->set($key, $value);
                //Check if the route required paramters are present
                foreach($route->getRequiredParameters() as $requiredParam){
                    if(!in_array($requiredParam, array_keys($this->parameters)))
                        throw new RouteException($requiredParam, RouteException::MISSING_REQUIRED_PARAMETER);
                }
                
            }else{

                throw InternalException::NoRouteFound();
            }
        }
        return $this->route;
    }

    public function getMethod(){
        return $_SERVER["REQUEST_METHOD"];
    }

    public function isXMLHttpRequest(){
        
		return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
    }
}
