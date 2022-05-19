<?php
namespace VPFramework\Routing;

use InvalidArgumentException;
use ReflectionClass;
use VPFramework\Routing\Annotations\Route as RouteAnnotation;
use VPFramework\Routing\RouteGroup;
use VPFramework\Routing\Annotations\RouteGroup as RouteGroupAnnotation;
use VPFramework\Routing\Route;
use VPFramework\Utils\AnnotationReader;
use VPFramework\Utils\ClassUtil;

class Loader{
    

    /**
     * Retourne un tableau de routes venant du fichier
     * Le fichier doit avoir un instruction return []
     * @param string $filePath
     * 
     * @return array
     */
    public static function fromFile(string $filePath):array
    {
        $fileReturn = require $filePath;
        if($fileReturn == null || !is_array($fileReturn)){
            throw new InvalidArgumentException("Le fichier '$filePath' ne retourne pas de tableau");
        }
        $routes = [];
        foreach($fileReturn as $route){
            if($route instanceof RouteGroup){
                foreach($route->getRoutes() as $r){
                    $routes[$r->getName()] = $r;
                }
            }elseif($route instanceof Route){
                $routes[$route->getName()] = $route;
            }
        }
        return $routes;
    }

    /**
     * Retourne un tableau de routes à partir d'une classe
     * Le fichier doit avoir un instruction return []
     * @param string $class
     * 
     * @return array
     */
    public static function fromClass(string $class):array
    {
        if(!class_exists($class, true)){
            throw new InvalidArgumentException("La classe '$class' n'a pas été retrouvée'");
        }
        $routes = [];
        $routeGroupAnnotation = AnnotationReader::getClassAnnotation($class, RouteGroupAnnotation::class);
        $pathStart = $routeGroupAnnotation != null ? $routeGroupAnnotation->pathStart : "";
        $reflectClass = new ReflectionClass($class);
        foreach($reflectClass->getMethods() as $method){
            $methodName = $method->getName();
            $routeAnnotation = AnnotationReader::getMethodAnnotation($class, $methodName, RouteAnnotation::class);
            if($routeAnnotation != null){
                $controllerName = ClassUtil::getSimpleName($class);
                $route = new Route($routeAnnotation->name, "$controllerName:$methodName", $pathStart.$routeAnnotation->path);
                $routes[$route->getName()] = $route;
            }
        }
        return $routes;
    }
}