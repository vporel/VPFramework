<?php

namespace VPFramework\Core\Configuration;

const SERVICES_FILE = ROOT."/config/services.json";
class ServiceConfiguration{
    private $services = null;

    public function getService($name){
        if($this->services === null){
            $this->services = json_decode(file_get_contents(SERVICES_FILE), true);
        }
        if($this->services !== null && array_key_exists($name, $this->services))
            return $this->services[$name];
        else 
            throw new ServiceNotFoundException($name);
    }
}