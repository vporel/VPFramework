<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\RouteConfiguration;
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
     * @param string $routeName
     * @param array $options
     */
    protected function redirectRoute($routeName, $options = [])
    {
        $route = DIC::getInstance()->get(RouteConfiguration::class)->getRoute($routeName);
        header("Location: ".$route->getPath($options));
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