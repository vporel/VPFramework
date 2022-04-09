<?php
namespace VPFramework\Model\Doctrine;

use Doctrine\ORM\Tools\Setup;

require_once ROOT."/vendor/autoload.php";

class Config
{

    public static function getConfig()
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(array(ROOT."/app"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        // or if you prefer yaml or XML
        // $config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

        return $config;
    }
}