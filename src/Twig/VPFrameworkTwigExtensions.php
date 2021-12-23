<?php

namespace VPFramework\Twig;

use Bes\Twig\Extension\MobileDetectExtension;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use VPFramework\Core\DIC;
use VPFramework\Core\AppGlobals;
use VPFramework\Core\RouteConfiguration;
use VPFramework\Core\Request;

define('ASSETS_DIR', DIC::getInstance()->get(AppGlobals::class)->getAssetsDir());

class VPFrameworkTwigExtension extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals()
    {
        return [
            "app" => DIC::getInstance()->get(AppGlobals::class)
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("url", [$this, "getUrl"]), 
            new TwigFunction("assets", [$this, "fromAssets"]), 
            new TwigFunction("css", [$this, "includeCSS"]),
            new TwigFunction("js", [$this, "includeJS"])
        ];
    }

    public function getUrl($name, $options = [])
    {
       // var_dump($options);
        $route = DIC::getInstance()->get(RouteCoif(array_key_exists($name, $routes)){
        return $route->getPath($options);
        
    }

    public function fromAssets($element){
        return ASSETS_DIR."/".$element;
    }

    public function includeCSS($element){
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

    public function includeJS($element){
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
}

return [new VPFrameworkTwigExtension(), new MobileDetectExtension()];