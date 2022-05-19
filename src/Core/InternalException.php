<?php
namespace VPFramework\Core;

/**
 * 
 */
class InternalException extends \Exception
{
    const NO_ROUTE_FOUND = 0;
    /**
     * @param string $msg
     * @param int $code
     */
    private function __construct(int $code)
    {
        parent::__construct("Code : $code", $code);
    }

    public static function NoRouteFound(){
        return new InternalException(self::NO_ROUTE_FOUND);
    }

}