<?php
namespace VPFramework\Utils;

use ReflectionProperty;
use Throwable;

class ObjectReflection
{
    public static function getPropertyValue($object, $property)
    {
        try{
            $method = "get".ucfirst($property);
            return $object->$method();
        }catch(Throwable $e){ //Erreur avec la méthode get
            try{
                return $object->$property(); //S'il existe une méthode portant le nom de la propriété
            }catch(Throwable $e){
                //Utilisation de l'API de reflection
                $reflecProperty = new ReflectionProperty(get_class($object), $property);
                $reflecProperty->setAccessible(true);
                return $reflecProperty->getValue($object);
            }
        }
    }

    public static function setPropertyValue($object, $property, $value)
    {
        try{
            $method = "set".ucfirst($property);
            return $object->$method($value);
        }catch(Throwable $e){ //Erreur avec la méthode get
            //Utilisation de l'API de reflection
            $reflecProperty = new ReflectionProperty(get_class($object), $property);
            $reflecProperty->setAccessible(true);
            return $reflecProperty->setValue($object, $value);
        }
    }
}