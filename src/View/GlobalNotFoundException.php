<?php
namespace VPFramework\View;

class GlobalNotFoundException extends \Exception
{
    private $globalName;
    public function __construct($globalName)
    {
        $this->globalName = $globalName;
        parent::__construct("La globale $globalName n'est pas définie");
    }

    public function getGlobalName(){ return $this->globalName; }
}