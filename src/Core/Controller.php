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
        return DIC::getInstance()->get(TemplateEngine::class, ["viewDir" => $this->getViewDir()])->getEngine()->render($viewFile, $data);
    }
    /* GLOBALS GETTERS */
    final public function getGlobal($name){
        return DIC::getInstance()->get(View::class)->getGlobal($name);
    }

    final public function getUser(){
        return $this->getGlobal("app")->getUser();
    }

    final public function addFlash($key, $value){
        $this->getGlobal("app")->addFlash($key, $value); 
    }

    /**
     * Redirection vers une autre route
     * @param string $routeName
     * @param array $options
     */
    final protected function redirectRoute($routeName, $options = [])
    {
        $route = DIC::getInstance()->get(RouteConfiguration::class)->getRoute($routeName);
        header("Location: ".$route->getPath($options));
        exit(0);
    }

    /**
     * Redirection vers une URL
     * @param $url
     */
    final protected function redirect($url)
    {   
        header("Location: $url");
        exit(0);
    }
    
    /**
     * @return "Chemin relatif vers le dossier contenant les vues pour ce controller
     */
    protected function getViewDir()
    {
        return Constants::PROJECT_ROOT."/View";
    }

}