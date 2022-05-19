<?php
namespace VPFramework\Model\Doctrine;

use Doctrine\ORM\ORMSetup;
use VPFramework\Core\Constants;

require_once Constants::PROJECT_ROOT."/vendor/autoload.php";

class Config
{

    public static function getConfig()
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations

        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = ORMSetup::createAnnotationMetadataConfiguration(array(Constants::APP_DIR), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        // or if you prefer yaml or XML
        // $config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

        return $config;
    }
}