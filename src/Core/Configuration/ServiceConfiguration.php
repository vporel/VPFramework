<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Constants;

class ServiceConfiguration{
    private $services = null;
    private $defaultAppServices = null;

    public function getService($name){
        $name = strtolower($name);
        if($this->services === null){
            $this->services = require Constants::$APP_ROOT."/config/services.php";
            $this->defaultAppServices = require Constants::FRAMEWORK_ROOT."/DefaultApp/config/services.php";
        }
        if($this->services !== null && array_key_exists($name, $this->services)){
            
            if($name == "security"){
                //Prise en compte des règles définies par le framework dans le dossier DefaultApp (Ex:pour l'administration)
                $this->services[$name] = array_merge($this->services[$name], $this->defaultAppServices["security"]);
            }
            return $this->services[$name];
        }else 
            throw new ServiceNotFoundException($name);
    }
}