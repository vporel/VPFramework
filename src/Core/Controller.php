<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\Configuration;
use VPFramework\View\TemplateEngine;
use VPFramework\View\View;

abstract class Controller
{
    /**
     * Génère la vue associé au contrôleur
     * @param string $viewFile Données utilisées pour générer la vue
     * @param array $data Données utilisées pour générer la vue
     */
    protected function render($viewFile, $data = [])
    {
        return DIC::getInstance()->get(TemplateEngine::class)->getEngine()->render($viewFile, $data);
    }
    /* GLOBALS GETTERS */
    public function getGlobal($name){
        return DIC::getInstance()->get(View::class)->getGlobal($name);
    }

    public function getUser(){
        return $this->getGlobal("app")->getUser();
    }

    public function addFlash($key, $value){
        $this->getGlobal("app")->addFlash($key, $value); 
    }

    /**
     * Redirection vers une autre route
     * @param $controller
     * @param $options
     */
    protected function redirectRoute($routeName, $options = [])
    {
        $routes = DIC::getInstance()->get(Configuration::class)->getRoutes();
        if(array_key_exists($routeName, $routes)) 
            header("Location: ".$routes[$routeName]->getPath($options));
        else
            throw new \Exception("La route $routeName n'existe pas");
    }

    /**
     * Redirection vers une URL
     * @param $url
     */
    protected function redirect($url)
    {   
        header("Location: $url");
    }

}