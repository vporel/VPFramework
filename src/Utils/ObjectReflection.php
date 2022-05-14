<?php
namespace VPFramework\Utils;

use ReflectionProperty;

class ObjectReflection
{
    public static function getPropertyValue($object, $property)
    {
        $methodsToTry = ["get".ucfirst($property), "is".ucfirst($property), $property];
        foreach($methodsToTry as $method){
            if(method_exists($object, $method))
                return $object->$method();
        }
        //Si aucune méthode trouvée, Utilisation de l'API de reflection
        $reflecProperty = new ReflectionProperty(get_class($object), $property);
        $reflecProperty->setAccessible(true);
        return $reflecProperty->getValue($object);  
    }

    public static function setPropertyValue($object, $property, $value)
    {
        $method = "set".ucfirst($property);
        if(method_exists($object, $method)){
            return $object->$method($value);
        }else{
            //Utilisation de l'API de reflection
            $reflecProperty = new ReflectionProperty(get_class($object), $property);
            $reflecProperty->setAccessible(true);
            return $reflecProperty->setValue($object, $value);
        }
    }
}