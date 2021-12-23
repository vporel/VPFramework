<?php

namespace VPFramework\Core\Configuration;


const APP_FILE = ROOT."/config/app.json";

class AppConfiguration 
{
    
   

    private
        $app = null;

    /**
     * The data returned by this functions are from the app.json file
     */
    public function get($name){
        if($this->app === null)
            $this->app = json_decode(file_get_contents(APP_FILE), true);
        if($this->app !== null && array_key_exists($name, $this->app))
            return $this->app[$name];
        else
            throw new AppParameterNotFoundException($name);
    }

    

    
    
}