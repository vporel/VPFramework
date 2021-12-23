<?php
namespace VPFramework\Core\Configuration;

class RouteNotFoundException extends \Exception
{
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct("LA route $name n'existe pas");
    }

    public function getRouteName(){ return $this->name; }
}