<?php

namespace VPFramework\Core\Routing;

use VPFramework\Core\Constants;
use VPFramework\Core\DIC;

/**
 * Cette classe définit une route dans l'application
 */
class Route
{
    private $name, $path, $controllerClass, $controllerMethod, $pathParams;

    private $pathRegex;
    private $controller;
        
    /**
     * __construct
     * @param  string $name
     * @param  string $controllerAndMethod Ex : HomeController:index
     * @param  string $path
     * @return void
     */
    public function __construct(string $name, string $controllerAndMethod, string $path)
    {
        $this->name = $name;
        $this->path = $path;
        $explodeControllerAndMethod = explode(":", $controllerAndMethod);
        if(count($explodeControllerAndMethod) == 2){
            $this->controllerClass = Constants::CONTROLLER_NAMESPACE."\\".$explodeControllerAndMethod[0];
            $this->controllerMethod = $explodeControllerAndMethod[1];
        }else{
            throw new RouteException($this->getName(), "controllerAndMethod($controllerAndMethod)", RouteException::WRONG_PARAMETER);
        }
        $this->pathParams = $this->findPathParams($this->path);
        $this->pathRegex = "#^";
        $path = $this->path;

        //Construction de l'expression régulière à tester sur l'URL
        foreach($this->pathParams as $param){
            $regex = $param["regex"];
            $firstChar = substr($regex, 0, 1);
            $lastChar = substr($regex, -1);
            $beforeRegex = ($param["default"] != null) ? "(" : "";
            $afterRegex = ($param["default"] != null) ? ")?" : "";
            if($firstChar == "^") //le paramètre doit commencer par cette chaine
                $regex = substr($regex, 1);
            else // On peut avoir un caractère avant
                $regex = ".*".$regex;
            if($lastChar == "$") //le paramètre doit se terminer par cette chaine
                $regex = substr($regex, 0, -1);
            else // On peut avoir un caractère après
                $regex = $regex.".*";
            $path = str_replace("/<".$param["all"].">", "(/".$regex.")".(($param["default"] != null) ? "?" : ""), $path);
            
        }
        $this->pathRegex .= $path."$#i";
    }

    public function getName(){
        return $this->name;
    }

    public function getControllerMethod(){
        return $this->controllerMethod;
    }

    /**
     * @return array Les paramètres dans le chemin
     */
    private function findPathParams($path){
        $pathParams = [];
        if(preg_match("#<([^>]+)>#", $path, $matches)){
            foreach(array_slice($matches, 1) as $match){
                $pathParam = [];
                $pathParam["all"] = $match;
                $pathParam["default"] = null;
                $pathParam["regex"] = ".+"; // PAr défaut, nimporte quelle suite de caractères
                $explodeForDefaultValue = explode("=", $pathParam["all"]);
                if(count($explodeForDefaultValue) > 1){
                    if(count($explodeForDefaultValue) == 2){
                        $pathParam["default"] = $explodeForDefaultValue[1];
                    }else{
                        throw new RouteException($this->name, $pathParam["all"], RouteException::INVALID_PATH_PARAMETER_EXCEPTION);
                    }
                }

                $explodeForRegex = explode("#", $pathParam["all"]);
                if(count($explodeForRegex) > 1){
                    if(count($explodeForRegex) == 3){
                        $pathParam["regex"] = $explodeForRegex[1];
                    }else{
                        throw new RouteException($this->name, $pathParam["all"], RouteException::INVALID_PATH_PARAMETER_EXCEPTION);
                    }
                }
                $explodeForName = explode("#", $explodeForDefaultValue[0]);
                $pathParam["name"] = $explodeForName[0];
                $pathParams[] = $pathParam;
            }
        }
        return $pathParams;
    }

    /**
     * @return Controller le controller appelé par la route
     */
    public function getController()
    {
        if($this->controller == null){
            if(!class_exists($this->controllerClass, true)){
                throw new RouteException($this->name, $this->controllerClass, RouteException::CONTROLLER_NOT_FOUND);
            }else{
                $this->controller = DIC::getInstance()->get($this->controllerClass);
            }
            if(!method_exists($this->controller, $this->controllerMethod)){
                throw new RouteException($this->name, $this->controllerMethod, RouteException::CONTROLLER_METHOD_NOT_FOUND);
            }
        }
        return $this->controller;
    }

    /**
     * Retourne le chemin en remplacant les paramètres par de véritables valeurs
     * @param array $params
     */
    public function getPath($params = []): string
    {
        $matchedParamsNb = 0;
        $getParameters = []; // Tableau des paramètres qui seront passés par méthode GET s'ils ne font pas partie des paramètres définis dans la route
        foreach($this->pathParams as $param)
            if(!in_array($param, array_keys($params)))
                throw new \Exception("L'option $param n'a pas été renseignée (".$this->path.")");
            else
                $matchedParamsNb += 1;
        if($matchedParamsNb != count($params)){
            foreach($params as $key => $value)
                if(!in_array($key, array_keys($this->pathParams)))
                    $getParameters[] = $key."=".$value;
        }
        $path = $this->path;
        foreach($this->pathParams as $param){
            if(in_array($param, array_keys($this->pathParamsRegex)) && !preg_match("#$paramRegex#", $params[$key]))
                   throw new InvalidURLParamException($param, $params[$param]);
            $path = str_replace("{".$param."}", $params[$param], $path);
        }
        if(count($getParameters) > 0) $path .= "?".implode("&", $getParameters);
        return $path;
    }

    /**
     * @return Expression régulière complète pour la route
     */
    public function getPathRegex(){
        return $this->pathRegex;
    }

    public function getData($matches){
        $data = [];
        if(count($this->pathParams) > 0){
            if(count($matches) > 0){
                $i = 0; 
                foreach($this->pathParams as $param){
                    $data[$param["name"]] = substr($matches[$i], 1);
                
                    $i++;
                }
            }else{
                $lastParam = end($this->pathParams);
                $data[$lastParam["name"]] = $lastParam["default"];
            }
        }
        return $data;
    }
}