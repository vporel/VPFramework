<?php

namespace VPFramework\Core\Routing;

use VPFramework\Core\DIC;

/**
 * Cette classe définit une route dans l'application
 */
define("CONTROLLER_NAMESPACE", "App\\Controller");
class Route
{
    private $name, $path, $controllerClassName, $controllerMethod, $pathParams;

    private $pathRegex;
    private $controller;
        
    /**
     * __construct
     * Les paramètes (champ pathParams doivent suivre l'ordre d'apparition dans le chemin (path))
     * @param  mixed $name
     * @param  mixed $controllerClassName
     * @param  mixed $controllerMethod
     * @param  mixed $path
     * @param  mixed $pathParams Un tableau associant à chaque paramètre du chemin une expression regulière pour vérifier sa validité. Ex : ["id" => "^[1-9][0-9]*$"] Pas d'options, pas de caractère de délimitation. par défaut, ces regex sont case insensitive
     * @return void
     */
    public function __construct(string $name, string $controllerClassName, string $controllerMethod, string $path, array $pathParams = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->controllerClassName = $controllerClassName;
        $this->controllerMethod = $controllerMethod;
        $this->pathParams = $pathParams;
        $this->optionsRegex = isset($content["options"]) ? $content["options"] : [];
        $this->pathRegex = "#^";
        $path = $this->path;
        //Construction de l'expression régulière à tester sur l'URL
        foreach($this->getAllPathParams() as $param){
            if(in_array($param, $this->pathParams)){
                $regex = $this->pathParams[$param];
                $firstChar = substr($regex, 0, 1);
                $lastChar = substr($regex, -1);
                if($firstChar == "^") //le paramètre doit commencer par cette chaine
                    $regex = substr($regex, 1);
                else // On peut avoir un caractère avant
                    $regex = ".*"+$regex;
                if($lastChar == "$") //le paramètre doit se terminer par cette chaine
                    $regex = substr($regex, 0, -1);
                else // On peut avoir un caractère avant
                    $regex = $regex+".*";
                $path = str_replace("{".$param."}", "(".$regex.")", $path);
            }else{
                $path = str_replace("{".$param."}", "(.+)", $path);
            }
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
    private function getAllPathParams(){
        $path = $this->path;
        $path = "#".preg_replace("#{[^}]+}#", "({.*})", $path)."#";
        if(preg_match($path, $this->path, $matches)){
            return array_slice($matches, 1);
        }else
            return [];
    }

    /**
     * @return Controller le controller appelé par la route
     */
    public function getController()
    {
        if($this->controller === null){
            $completeControllerClassName = CONTROLLER_NAMESPACE."\\".$this->controllerClassName;
            if(!class_exists($completeControllerClassName)){
                throw new ControllerNotFoundException($completeControllerClassName);
            }else{
                $this->controller = DIC::getInstance()->get($completeControllerClassName);
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
        foreach(array_keys($this->pathParams) as $key)
            if(!in_array($key, array_keys($params)))
                throw new \Exception("L'option $key n'a pas été renseignée (".$this->path.")");
            else
                $matchedParamsNb += 1;
        if($matchedParamsNb != count($params))
            foreach($params as $key => $value)
                if(!in_array($key, array_keys($this->pathParams)))
                    $getParameters[] = $key."=".$value;
        $path = $this->path;
        foreach($this->pathParams as $key => $regex){
            if(preg_match("#$regex#", $params[$key])){
                $path = str_replace("{".$key."}", $params[$key], $path);
            }else{
                throw new InvalidURLParamException($key, $params[$key]);
            }
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
        $i = 0; 
        foreach($this->pathParams as $option => $regex){
            $data[$option] = $matches[$i];
            $i++;
        }
        return $data;
    }
}