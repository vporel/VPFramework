<?php
namespace VPFramework\Core\Routing;

class RouteException extends \Exception
{
    const CONTROLLER_NOT_FOUND = 0,
        CONTROLLER_METHOD_NOT_FOUND = 1,
        WRONG_PARAMETER = 2,
        INVALID_PATH_PARAMETER_EXCEPTION = 3;
    /**
     * @param string $routeName
     * @param string $element
     * @param int $code
     */
    public function __construct(string $routeName, string $element, int $code)
    {
        if($element == "")
            $msg = "Code : $code";
        else{
            $msg = "Route ($routeName) : ";
            switch($code){
                case self::CONTROLLER_NOT_FOUND: $msg .= "CONTROLLER_NOT_FOUND - Le contrôleur $element n'existe pas";break;
                case self::CONTROLLER_METHOD_NOT_FOUND: $msg .= "CONTROLLER_METHOD_NOT_FOUND - La méthode $element n'a pas été trouvée dans le contrôleur défini";break;
                case self::WRONG_PARAMETER: $msg .= "WRONG_PARAMETER - Le paramètre $element n'a pas été correctement défini";break;
                case self::INVALID_PATH_PARAMETER_EXCEPTION: $msg .= "INVALID_PATH_PARAMETER_EXCEPTION - Le paramètre $element n'a pas été correctement défini";break;
            }
        }
        parent::__construct($msg, $code);
    }

}