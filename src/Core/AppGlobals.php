<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\AppConfiguration;
use VPFramework\Utils\FlexibleClassTrait;

class AppGlobals
{
    use FlexibleClassTrait;

    private $config;
    private $user = null;
    public function __construct(AppConfiguration $config)
    {
        $this->config = $config;
    }
    public function getName()
    {
        return $this->config->get("appName");
    }

    public function getUser()
    {
        if($this->user == null){
            if(isset($_SESSION["user"]) && isset($_SESSION["user"]["keyProperty"])){
                $this->user = DIC::getInstance()->get($_SESSION["user"]["repository"])->findOneBy([$_SESSION["user"]["keyProperty"] => $_SESSION["user"]["keyPropertyValue"]]);
            }
        }
        return $this->user;
    }

    public function getFlash($key)
    {
        if(!isset($_SESSION["flashes"]))
            $_SESSION["flashes"] = [];
        if(isset($_SESSION["flashes"][$key])){
            $val = $_SESSION["flashes"][$key];
            unset($_SESSION["flashes"][$key]);
            return $val;
        }
        return null;
    }

    public function addFlash($key, $value)
    {
        if(!isset($_SESSION["flashes"]))
            $_SESSION["flashes"] = [];
        $_SESSION["flashes"][$key] = $value;
    }

    public function existFlash($key)
    {
        if(!isset($_SESSION["flashes"]))
            $_SESSION["flashes"] = [];
        return isset($_SESSION["flashes"][$key]);
    }

    public function getAssetsDir()
    {
        $urlPath = $_SERVER["REQUEST_URI"];
        $parts = explode("/", $urlPath);
        $assetsDir = "";
        for($i = 1;$i<count($parts); $i++){
            $assetsDir .= "/..";
        }
        return $assetsDir;
    }
    
}