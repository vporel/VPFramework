<?php
namespace VPFramework\Core;

use InvalidArgumentException;

class RouteException extends \Exception
{
    const CONTROLLER_NOT_FOUND = 0,
        CONTROLLER_METHOD_NOT_FOUND = 1,
        MISSING_REQUIRED_PARAMETER = 2;
    /**
     * @param string $routeName
     * @param string $element
     * @param int $code
     */
    public function __construct(string $element, int $code)
    {
        if($element == null)
            throw new InvalidArgumentException("L'élément doit etre un cahine de caractère non vide");
        switch($code){
            case self::CONTROLLER_NOT_FOUND: $msg = "CONTROLLER_NOT_FOUND - Le contrôleur $element n'existe pas";break;
            case self::CONTROLLER_METHOD_NOT_FOUND: $msg = "CONTROLLER_METHOD_NOT_FOUND - La méthode $element n'a pas été trouvée dans le contrôleur défini";break;
            case self::MISSING_REQUIRED_PARAMETER: $msg = "MISSING_REQUIRED_PARAMETER - Le paramètre $element n'est pas présent dans la requête";break;
            default: throw new InvalidArgumentException("Le code $code n'est pas reconnu");
        }
        parent::__construct($msg, $code);
    }

}