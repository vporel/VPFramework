<?php

namespace VPFramework\Core\Routing;

use VPFramework\Core\DIC;

/**
 * Cette classe définit une route dans l'application
 */
class Route
{
    private $name, $path, $controllerClass, $controllerMethod, $pathParams, $pathParamsRegex;

    private $pathRegex;
    private $controller;
        
    /**
     * __construct
     * Les paramètes (champ pathParams doivent suivre l'ordre d'apparition dans le chemin (path))
     * @param  mixed $name
     * @param  mixed $controllerClass Ex : HomeController::class
     * @param  mixed $controllerMethod
     * @param  mixed $path
     * @param  mixed $pathParams Un tableau associant à chaque paramètre du chemin une expression regulière pour vérifier sa validité. Ex : ["id" => "^[1-9][0-9]*$"] Pas d'options, pas de caractère de délimitation. par défaut, ces regex sont case insensitive
     * @return void
     */
    public function __construct(string $name, string $controllerClass, string $controllerMethod, string $path, array $pathParamsRegex = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
        $this->pathParamsRegex = $pathParamsRegex;
        $this->findAllPathParams();
        $this->pathRegex = "#^";
        $path = $this->path;

        //Construction de l'expression régulière à tester sur l'URL
        foreach($this->pathParams as $param){
            if(in_array($param, $this->pathParamsRegex)){
                $regex = $this->pathParamsRegex[$param];
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
    private function findAllPathParams(){
        $regex = "#".preg_replace("#{[^}]+}#", "({.*})", $this->path)."#";
        if(preg_match($regex, $this->path, $matches)){
            $this->pathParams = [];
            foreach(array_slice($matches, 1) as $match){
                $this->pathParams[] = substr(substr($match, 1), 0, -1);
            }
        }else
            $this->pathParams = [];
    }

    /**
     * @return Controller le controller appelé par la route
     */
    public function getController()
    {
        if($this->controller == null){
            if(!class_exists($this->controllerClass, true)){
                throw new ControllerNotFoundException($this->controllerClass);
            }else{
                $this->controller = DIC::getInstance()->get($this->controllerClass);
            }
            if(!method_exists($this->controller, $this->controllerMethod)){
                throw new ControllerMethodNotFoundException($this->controllerClass, $this->controllerMethod);
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
        $i = 0; 
        foreach($this->pathParams as $param){
            $data[$param] = $matches[$i];
            $i++;
        }
        return $data;
    }
}