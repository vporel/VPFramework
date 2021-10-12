<?php

namespace VPFramework\Core;

use VPFramework\Core\Route\Route;

if(!defined("ROOT")){
     define("ROOT" , __DIR__."/../../..");
}

session_start();

class Router
{
    private 
        $request, 
        $config, 
        $controller, 
        $controllerAction, 
        $DIC;
    public function __construct(Request $request, Configuration $config){
        $this->DIC = DIC::getInstance();

        $this->request = $request;
        $this->config = $config;
        if($this->checkSecurity($this->request->getUrlPath())){
            $this->controller = $this->createController();
        }
        
    }

    public function end(){
        echo $this->DIC->invoke($this->controller, $this->controllerAction);
    }

    /**
     * Création du contrôleur en fonction de la requête reçue
     * @return string|Controler
     */
    public function createController()
    {
        $route = $this->request->getRoute();
        $action = $route->getAction();
        $controller = $action["controller"];
        $this->controllerAction = $action["action"];
        $controllerClass = "App\\Controller\\".$controller;
        $controllerFile = ROOT."/app/Controller/".$controller.".php";
        if(\file_exists($controllerFile))
        {
            $controller = $this->DIC->get($controllerClass);
            return $controller;
        } else
            throw new \Exception("Fichier $controllerFile introuvable introuvable");
        
    }
    
    public function checkSecurity($urlPath)
    {
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
        return true;
    }
}