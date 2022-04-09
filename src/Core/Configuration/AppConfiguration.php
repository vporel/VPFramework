<?php

namespace VPFramework\Core\Configuration;

/**
 * Récupération de la configuration de l'application
 */
class AppConfiguration 
{
    
   

    private
        $app = null;

    /**
     * The data returned by this functions are from the app.json file
     */
    public function get($name){
        if($this->app === null){
            $this->app = require ROOT."/config/app.php";
        }
        if($this->app !== null && array_key_exists($name, $this->app))
            return $this->app[$name];
        else
            throw new AppParameterNotFoundException($name);
    }

    

    
    
}