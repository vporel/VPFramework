<?php
namespace VPFramework\Core\Routing\Security;

class SecurityException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }

}