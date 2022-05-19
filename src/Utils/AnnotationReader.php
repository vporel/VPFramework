<?php
namespace VPFramework\Utils;

use ReflectionClass;
use ReflectionProperty;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use ReflectionMethod;

/**
 * Get annotations defined on a class or class property
 * @author NKOUANANG KEPSEU VIVIAN POREL <dev.vporel@gmail.com>
 */
class AnnotationReader
{  
    private static $annotationReader;

    /**
     * @param string $class
     * @param string $annotationClass
     * 
     * @return object|null
     */
    public static function getClassAnnotation(string $class, string $annotationClass):?object
    {
        $reflectClass = new ReflectionClass($class);
        $annotation = self::getAnnotationReader()->getClassAnnotation($reflectClass, $annotationClass);
        return $annotation;
    }

    /**
     * @param string $class
     * @param string $property
     * @param string $annotationClass
     * 
     * @return object|null
     */
    public static function getPropertyAnnotation(string $class, string $property, string $annotationClass):?object
    {
        $reflectProperty = new ReflectionProperty($class, $property);
        $annotation = self::getAnnotationReader()->getPropertyAnnotation($reflectProperty, $annotationClass);
        return $annotation;
    }

    /**
     * @param string $class
     * @param string $property
     * @param string $annotationClass
     * 
     * @return object|null
     */
    public static function getMethodAnnotation(string $class, string $method, string $annotationClass):?object
    {
        $reflectMethod = new ReflectionMethod($class, $method);
        $annotation = self::getAnnotationReader()->getMethodAnnotation($reflectMethod, $annotationClass);
        return $annotation;
    }

    /**
     * @return DoctrineAnnotationReader
     */
    private static function getAnnotationReader():DoctrineAnnotationReader
    {
        if(self::$annotationReader == null)
            self::$annotationReader = new DoctrineAnnotationReader();
        return self::$annotationReader;
    }
}