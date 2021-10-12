<?php

namespace VPFramework\Core;

use App\Twig\TwigExtension;
use Twig_Environment;
use Twig_Loader_Filesystem;

const VIEW_DIR = __DIR__."/../../../View";

if(!defined('ROOT'))
    define('ROOT', __DIR__."/../../..");


abstract class Controller
{

    protected $twig;

    /**
     * Génère la vue associé au contrôleur
     * @param string $viewFile Données utilisées pour générer la vue
     * @param array $data Données utilisées pour générer la vue
     */
    protected function render($viewFile, $data = [])
    {
        
        return $this->getTwig()->render($viewFile,$data);
    }

    protected function getTwig(): Twig_Environment
    {
        if($this->twig === null){
            $loader = new Twig_Loader_Filesystem(VIEW_DIR);
            $this->twig = new Twig_Environment($loader, [
                "cache" => false,
                "autoescape" => false
            ]);
            $extensions = require ROOT."/vendor/VPFramework/Twig/VPFrameworkTwigExtensions.php";
            //Chargement de l'extensio de l'application
            if(file_exists(ROOT."/app/twig/TwigExtensions.php")){
                $extensions = array_merge($extensions, require ROOT."/app/twig/TwigExtensions.php");
            }
            foreach($extensions as $extension)
                    $this->twig->addExtension($extension);
        }
        return $this->twig;
    }

    /* GLOBALS GETTERS */
    public function getGlobal($name){
        $globals = $this->getTwig()->getGlobals();
        if(array_key_exists($name, $globals))
            return $globals[$name];
        else
            throw new \Exception("La variable globale $name n'est pas");
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