<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Constants;

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
            $this->app = require Constants::PROJECT_ROOT."/Config/app.php";
        }
        if($this->app !== null && array_key_exists($name, $this->app))
            return $this->app[$name];
        else
            throw new AppParameterNotFoundException($name);
    }

    

    
    
}