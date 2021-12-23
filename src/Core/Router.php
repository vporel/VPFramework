<?php

namespace VPFramework\Core;

use VPFramework\Service\Security\Security;

if(!defined("ROOT")){
     define("ROOT" , __DIR__."/../../../../..");
}

session_start();

class Router
{
    private $request;
    public function __construct(Request $request){

        $this->request = $request;
        
    }

    public function end(Security $security){
        if($security->checkSecurity($this->request->getUrlPath())){
            $controller = DIC::getInstance()->get($this->request->getRoute()->getController());
            echo DIC::getInstance()->invoke($controller, $this->request->getRoute()->getControllerAction());
        }
    }
    
    
}