<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\Configuration;
use VPFramework\Core\Configuration\ServiceNotFoundException;
use VPFramework\Core\Route\Route;

if(!defined("ROOT")){
     define("ROOT" , __DIR__."/../../../../..");
}

session_start();

class Router
{
    private 
        $request, 
        $config, 
        $DIC;
    public function __construct(Request $request, Configuration $config){
        $this->DIC = DIC::getInstance();

        $this->request = $request;
        $this->config = $config;
        
    }

    public function end(){
        if($this->checkSecurity($this->request->getUrlPath())){
            $controller = $this->DIC->get($this->request->getRoute()->getController());
            echo $this->DIC->invoke($controller, $this->request->getRoute()->getControllerAction());
        }
    }
    
    public function checkSecurity($urlPath)
    {
        try{
            $security = $this->config->getService("security");
            foreach($security["safe_urls"] as $safeUrl => $requirements){
                if(preg_match($safeUrl, $urlPath)){
                    $user = $this->DIC->get(AppGlobals::class)->getUser(); 
                    if($user == null || !in_array($user->getRole(), $requirements["roles"]) || !($user instanceof $requirements["entity"])){
                        unset($_SESSION["user"]);
                        header("Location: ".$security["login_paths"][$safeUrl]);
                    }
                    break;
                }
                if(in_array($urlPath, array_values($security["login_paths"])))
                {
                    $fullUrl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "") ? "https" : "http";
                    $fullUrl .= "://".$_SERVER["HTTP_HOST"].$urlPath;
                    if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] != $fullUrl){
                        $_SESSION["before-login-url"] = $_SERVER["HTTP_REFERER"];
                    }else{
                        $_SESSION["before-login-url"] = $_SESSION["before-login-url"] ?? "";
                    }
                }
            }
        }catch(ServiceNotFoundException $e){}
        return true;
    }
}