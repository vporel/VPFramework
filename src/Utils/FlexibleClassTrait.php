<?php
namespace VPFramework\Utils;

use ReflectionProperty;
use Throwable;

trait FlexibleClassTrait
{
    public function __get($property)
    {
        try{
            $method = "get".ucfirst($property);
            return $this->$method();
        }catch(Throwable $e){ //Erreur avec la méthode get
            try{
                return $this->$property(); //S'il existe une méthode portant le nom de la propriété
            }catch(Throwable $e){
                //Utilisation de l'API de reflection
                $reflecProperty = new ReflectionProperty(get_called_class(), $property);
                $reflecProperty->setAccessible(true);
                return $reflecProperty->getValue();
            }
        }
    }
}