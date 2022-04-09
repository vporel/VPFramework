<?php
namespace VPFramework\Core\Configuration;

class VPFrameworkConfigurationException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }

}