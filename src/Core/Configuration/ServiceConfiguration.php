<?php

namespace VPFramework\Core\Configuration;

use VPFramework\Core\Constants;

class ServiceConfiguration{
    private $services = null;
    private $defaultAppServices = null;

    public function getService($name){
        $name = strtolower($name);
        if($this->services === null){
            $this->services = require Constants::PROJECT_ROOT."/Config/services.php";
            $this->defaultAppServices = require Constants::FRAMEWORK_ROOT."/DefaultApp/Config/services.php";
        }
        if($this->services !== null && array_key_exists($name, $this->services)){
            
            if($name == "security" || $name == "admin"){
                //Prise en compte des règles définies par le framework dans le dossier DefaultApp (Ex:pour l'administration)
                $this->services[$name] = array_merge($this->services[$name], $this->defaultAppServices[$name]);
            }
            return $this->services[$name];
        }else 
            throw new ServiceNotFoundException($name);
    }
}