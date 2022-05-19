<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Core;

use VPFramework\Service\Security\Security;

session_start();


/**
 * [Description Router]
 * @author NKOUANANG KEPSEU VIVIAN POREL (dev.vporel@gmail.com)
 */
class Router
{
    private $request;
    public function __construct(Request $request){

        $this->request = $request;
        
    }

    public function end(Security $security){
        try{
            if($security->requireAccess($this->request->getUrlPath())){
                $route = $this->request->getRoute();
                $controller = $route->getController();
                echo DIC::getInstance()->invoke($controller, $this->request->getRoute()->getControllerMethod());
            
            }
        }catch(InternalException $e){
            if($e->getCode() == InternalException::NO_ROUTE_FOUND)
                require Constants::FRAMEWORK_ROOT."/View/views/defaultView.php";
        }
    }

    /**
     * @param string PROJECT_ROOT Le dossier racine de l'application
     */
    public static function start(){
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace("VPFramework\\Model\\Entity\\Annotations", Constants::FRAMEWORK_ROOT."/Model/Entity/Annotations");
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader("class_exists");
        $DIC = DIC::getInstance();
        $router = $DIC->get(Router::class);
        $DIC->invoke($router, "end");
    }
    
    
}