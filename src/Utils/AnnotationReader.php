<?php
namespace VPFramework\Utils;

use ReflectionProperty;

class AnnotationReader
{  
    private static $annotationReader;
    public static function getPropertyAnnotation($entityClass, $property, $annotationClass){
        $reflectProperty = new ReflectionProperty($entityClass, $property);
        $annotation = self::getAnnotationReader()->getPropertyAnnotation($reflectProperty, $annotationClass);
        if($annotation != null){
            return $annotation;
        }else{
            return null;
        }
    }

    private static function getAnnotationReader(){
        if(self::$annotationReader == null)
            self::$annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
        return self::$annotationReader;
    }
}