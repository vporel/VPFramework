<?php
namespace VPFramework\Core\Configuration;

class ConfigurationException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }

}