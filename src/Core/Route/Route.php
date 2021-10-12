<?php

namespace VPFramework\Core\Route;

class Route
{
    private $path, $pathRegex, $action, $optionsRegex = [];
    public function __construct($content)
    {
        $this->path = $content["path"];
        $this->action = $content["action"];
        $this->optionsRegex = isset($content["options"]) ? $content["options"] : [];
        $this->pathRegex = "#^";
        $path = $this->path;
        foreach($this->optionsRegex as $key => $regex){
            $regex = str_replace("#^", "", $regex);
            $regex = str_replace("$#", "", $regex);
            $regex = str_replace("#", "", $regex);
            $path = str_replace("{".$key."}", "(".$regex.")", $path);
        }
        $this->pathRegex .= $path."$#i";
    }

    public function getPath($options = []): string
    {
        $matchedOptionsNb = 0;
        $getParameters = [];
        foreach(array_keys($this->optionsRegex) as $key)
            if(!in_array($key, array_keys($options)))
                throw new \Exception("L'option $key n'a pas été renseignée (".$this->path.")");
            else
                $matchedOptionsNb += 1;
        if($matchedOptionsNb != count($options))
            foreach($options as $key => $value)
                if(!in_array($key, array_keys($this->optionsRegex)))
                    $getParameters[] = $key."=".$value;
        $path = $this->path;
        foreach($this->optionsRegex as $key => $regex){
            if(preg_match($regex, $options[$key])){
                $path = str_replace("{".$key."}", $options[$key], $path);
            }else{
                throw new \Exception("URL non valide : la value ".$options[$key]." pour la clé $key n'est pas correcte");
            }
        }
        if(count($getParameters) > 0) $path .= "?".implode("&", $getParameters);
        return $path;
    }

    public function getPathRegex(){
        return $this->pathRegex;
    }

    /**
     * @return array
     */
    public function getAction(): array
    {
        $action = explode(":", $this->action);
        return ["controller" => $action[0], "action" => $action[1]];
    }

    public function getData($matches){
        $data = [];
        $i = 1;
        foreach($this->optionsRegex as $option => $regex){
            $data[$option] = $matches[$i];
            $i++;
        }
        return $data;
    }
}