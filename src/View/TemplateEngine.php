<?php
namespace VPFramework\View;

use Twig\TwigFunction;
use Twig_Environment;
use Twig_Loader_Filesystem;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\DIC;
use VPFramework\Core\Configuration\ServiceNotFoundException;
use VPFramework\Core\Constants;

class TemplateEngine
{
    private $viewDir;
    /**
     * Contructeur
     * @param string $viewDir Dossier contenant les vues
     */
    public function __construct($viewDir){
        $this->viewDir = $viewDir;
    }

    public function getEngine()
    {
        $twig = $this->getTwig();
        if($twig != null) {
            return $twig;
        }
        return new ViewLoader($this->viewDir);
    }

    public function getTwig()
    {
        try{
            $twigConfig = DIC::getInstance()->get(ServiceConfiguration::class)->getService("twig");
            if($twigConfig["activated"]){
       
                $loader = new Twig_Loader_Filesystem($this->viewDir);
                $twig = new Twig_Environment($loader, $twigConfig);
                foreach($twigConfig["extensions"] as $extension)
                    $twig->addExtension(new $extension());
                $basicView = DIC::getInstance()->get(View::class);
                $functions = ["css", "url", "js", "assets"];
                foreach($functions as $function){
                    $twig->addFunction(new TwigFunction($function, [$basicView, $function]));
                }
                foreach($basicView->getGlobals() as $key => $value)
                    $twig->addGlobal($key, $value);
                return $twig;
            }
        }catch(ServiceNotFoundException $e){
            return null;
        }
    }
}