<?php
namespace VPFramework\Core\Routing;

class InvalidURLParamException extends \Exception
{
    public function __construct($key, $invalidValue)
    {
        parent::__construct("URL : la valeur $invalidValue pour la clé $key n'est pas correcte");
    }

}