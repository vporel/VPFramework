<?php
namespace VPFramework\View;

use VPFramework\Core\AppGlobals;
use VPFramework\Core\Configuration\AppParameterNotFoundException;
use VPFramework\Core\Configuration\Configuration;
use VPFramework\Core\DIC;

define('ASSETS_DIR', DIC::getInstance()->get(AppGlobals::class)->getAssetsDir());

class View

{
    private $globals = null;
    private $functions = [];

    public function __construct()
    {
        $this->globals = ["app" => DIC::getInstance()->get(AppGlobals::class)];
        try{
            $extensions = DIC::getInstance()->get(Configuration::class)->get("view_extensions");
            foreach($extensions as $ext){
                $extObj = new $ext();
                $this->globals = array_merge($this->globals, $extObj->getGlobals());
                $this->functions = array_merge($this->functions, $extObj->getFunctions());
            }
        }catch(AppParameterNotFoundException $e){

        }
    }

    public function getGlobals()
    {
        return $this->globals;
    }

    public function getGlobal($name)
    {
        if(array_key_exists($name, $this->globals))
            return $this->globals[$name];
        else
            throw new GlobalNotFoundException($name);
    }

    public function url($name, $options = [])
    {
       // var_dump($options);
        $routes = DIC::getInstance()->get(Configuration::class)->getRoutes();
        if(array_key_exists($name, $routes)){
            return $routes[$name]->getPath($options);
        }else{
            throw new \Exception("L'url pour $name n'a pas été trouvée. Vérifiez les routes de l'application");
        }
    }

    public function assets($element){
        return ASSETS_DIR."/".$element;
    }

    public function css($element){
        if(is_string($element))
            return '<link rel="stylesheet" type="text/css" href="'.ASSETS_DIR.'/'.$element.'"\/>';
        else {
            $html = '';
            foreach($element as $src){
                $html .= '<link rel="stylesheet" type="text/css" href="'.ASSETS_DIR.'/'.$src.'"\/>';
            }
            return $html;
        }
    }

    public function js($element){
        if(is_string($element))
            return '<script language="javascript" src="'.ASSETS_DIR.'/'.$element.'"></script>';
        else{
            $html = '';
            foreach($element as $src){
                $html .= '<script language="javascript" src="'.ASSETS_DIR.'/'.$src.'"></script>';
            }
            return $html;
        }
    }

    public function __call($name, $arguments)
    {
        return $this->functions[$name](...$arguments);
    }
}