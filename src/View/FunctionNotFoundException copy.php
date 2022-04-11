<?php
namespace VPFramework\View;

class FunctionNotFoundException extends \Exception
{
    public function __construct($name)
    {
        parent::__construct("La fonction $name n'est pas définie");
    }
}