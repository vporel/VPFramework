<?php
namespace VPFramework\Core\Configuration;

class AppParameterNotFoundException extends \Exception
{
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct("Le paramètre $name n'est pas défini dans le fichier de configuration de l'application");
    }

    public function getName(){ return $this->name; }
}