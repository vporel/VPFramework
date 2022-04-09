<?php

namespace VPFramework\Core\Configuration;
/**
 * Recupération des services configurés par le développeur
 */
class ServiceConfiguration{
    private $services = null;

    public function getService($name){
        if($this->services === null){
            $this->services = require ROOT."/config/services.php";
        }
        if($this->services !== null && array_key_exists($name, $this->services))
            return $this->services[$name];
        else 
            throw new ServiceNotFoundException($name);
    }
}