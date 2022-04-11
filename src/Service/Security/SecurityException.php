<?php
namespace VPFramework\Service\Security;

class SecurityException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }

}