<?php

namespace VPFramework\Core;

use VPFramework\Service\Security\Security;

session_start();

class Router
{
    private $request;
    public function __construct(Request $request){

        $this->request = $request;
        
    }

    public function end(Security $security){
        if($security->requireAccess($this->request->getUrlPath())){
            $route = $this->request->getRoute();
            if($route->getName() != Request::DEFAULT_ROUTE_NAME){
                $controller = $route->getController();
                echo DIC::getInstance()->invoke($controller, $this->request->getRoute()->getControllerMethod());
            }else{
                require Constants::FRAMEWORK_ROOT."/View/views/defaultView.php";
            }
        }
    }
    
    
}