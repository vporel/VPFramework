<?php
namespace VPFramework\View;

use VPFramework\Core\AppGlobals;
use VPFramework\Core\Configuration\AppParameterNotFoundException;
use VPFramework\Core\Configuration\AppConfiguration;
use VPFramework\Core\Configuration\RouteConfiguration;
use VPFramework\Core\DIC;

define('ASSETS_DIR', DIC::getInstance()->get(AppGlobals::class)->getAssetsDir());

class View

{
    private $globals = null;
    private $functions = [];

    public function __construct()
    {
        $this->globals = ["app" => DIC::getInstance()->get(AppGlobals::class)];

        $this->functions = $this->getBuiltinFunctions();

        try{
            $extensions = DIC::getInstance()->get(AppConfiguration::class)->get("view_extensions");
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

    public function getBuiltinFunctions(){
        return [
            "url" => function($name, $options = []){
                $route = DIC::getInstance()->get(RouteConfiguration::class)->getRoute($name);
                return $route->getPath($options);
            },
            "asset" => function($element){
                return ASSETS_DIR."/".$element;
            },
            "css" => function($element){
                if(is_string($element))
                    return '<link rel="stylesheet" type="text/css" href="'.ASSETS_DIR.'/'.$element.'"/>';
                else {
                    $html = '';
                    foreach($element as $src){
                        $html .= '<link rel="stylesheet" type="text/css" href="'.ASSETS_DIR.'/'.$src.'"/>';
                    }
                    return $html;
                }
            }, 
            "js" => function($element){
                if(is_string($element))
                    return '<script type="text/javascript" src="'.ASSETS_DIR.'/'.$element.'"></script>';
                else{
                    $html = '';
                    foreach($element as $src){
                        $html .= '<script type="text/javascript" src="'.ASSETS_DIR.'/'.$src.'"></script>';
                    }
                    return $html;
                }
            }
        ];
    }

    public function getFunctions(){
        return $this->functions;
    }

    public function getFunction($name){
        if(array_key_exists($name, $this->functions))
            return $this->functions[$name];
        else
            throw new FunctionNotFoundException($name);
    }

    public function __call($name, $arguments)
    {
        return $this->functions[$name](...$arguments);
    }
}