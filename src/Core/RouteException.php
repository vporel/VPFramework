<?php
namespace VPFramework\Core;

class RouteException extends \Exception
{
    const CONTROLLER_NOT_FOUND = 0,
        CONTROLLER_METHOD_NOT_FOUND = 1;
    /**
     * @param string $routeName
     * @param string $element
     * @param int $code
     */
    public function __construct(string $element, int $code)
    {
        if($element == "")
            $msg = "Code : $code";
        else{
            $msg = "Route : ";
            switch($code){
                case self::CONTROLLER_NOT_FOUND: $msg .= "CONTROLLER_NOT_FOUND - Le contrôleur $element n'existe pas";break;
                case self::CONTROLLER_METHOD_NOT_FOUND: $msg .= "CONTROLLER_METHOD_NOT_FOUND - La méthode $element n'a pas été trouvée dans le contrôleur défini";break;
            }
        }
        parent::__construct($msg, $code);
    }

}