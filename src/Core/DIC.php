<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\Configuration;
use VPFramework\Doctrine\Config;
use Doctrine\ORM\EntityManager;

if(!defined("ROOT"))
    define("ROOT", __DIR__."/../../../../..");

/**
 * Conteneur d'injection de dépendances dans l'application
 */

class DIC
{
    private $dependances = [];
    private $instances = [];
    private static $instance = null;

    public function __construct()
    {
        
    }

    public function get($name)
    {
        if(!array_key_exists($name, $this->instances)){
            if(!in_array($name, ["Doctrine\\ORM\\EntityManager", "Doctrine\\ORM\\EntityManagerInterface"])){
            
                if(array_key_exists($name, $this->dependances)){
                    $callable = $this->dependances[$name];
                    $this->instances[$name] = $callable();
                }else{
                    $this->dynamicInstanciation($name);
                }
            }else{
                $database = $this->get(Configuration::class)->get("database");

                // database configuration parameters
                $conn = array(
                    'driver' => 'pdo_mysql',
                    'user' => $database["username"],
                    'password' => $database["password"],
                    'dbname' => $database["name"],
                    'host' => $database["host"]
                );
                $this->instances[$name] = EntityManager::create($conn, Config::getConfig());
            }
        }
        return $this->instances[$name];
    }

    public function set(string $name, Callable $resolver)
    {
        $this->dependances[$name] = $resolver;
    }

    public function invoke($obj, $method, array $defaultValues = [])
    {
        $reflectedClass = new \ReflectionClass($obj);
        $reflectedMethod = $reflectedClass->getMethod($method);
        if($reflectedMethod){
            $parameters = $reflectedMethod->getParameters();
            $parametersValues = [];
            foreach($parameters as $param){
                if(!array_key_exists($param->getName(), $defaultValues)){
                    if($param->getClass())
                        $parametersValues[] = $this->get($param->getClass()->getName());
                    else
                        $parametersValues[] = $param->getDefaultValue();
                }else{
                    $parametersValues[] = $defaultValues[$param->getName()];
                }
            }
            return $obj->$method(...$parametersValues);
        }else{
            throw new \Exception("La méthode $method n'existe pas dans la classe ".$obj->getClass());
        }
    }

    public function invokeStatic($class, $method, array $defaultValues = [])
    {
        $reflectedClass = new \ReflectionClass($class);
        $reflectedMethod = $reflectedClass->getMethod($method);
        if($reflectedMethod){
            $parameters = $reflectedMethod->getParameters();
            $parametersValues = [];
            foreach($parameters as $param){
                if(!array_key_exists($param->getName(), $defaultValues)){
                    if($param->getClass())
                        $parametersValues[] = $this->get($param->getClass()->getName());
                    else
                        $parametersValues[] = $param->getDefaultValue();
                }else{
                    $parametersValues[] = $defaultValues[$param->getName()];
                }
            }
            return $class::$method(...$parametersValues);
        }else{
            throw new \Exception("La méthode $method n'existe pas dans la classe ".$class);
        }
    }

    public function dynamicInstanciation($class)
    {
        $reflectedClass = new \ReflectionClass($class);
        if($reflectedClass->isInstantiable()){
            $contructor = $reflectedClass->getConstructor();
            if($contructor !== null){
                $parameters = $contructor->getParameters();
                $parametersValues = [];
                foreach($parameters as $param){
                    if($param->getClass())
                        $parametersValues[] = $this->get($param->getClass()->getName());
                    else
                        $parametersValues[] = $param->getDefaultValue();
                }
                $this->instances[$class] = $reflectedClass->newInstanceArgs($parametersValues);                    
            }else{
                $this->instances[$class] = new $class;
            }
        }
    }

    public static function getInstance()
    {
        if(self::$instance == null)
            self::$instance = new DIC();
        return self::$instance;
    }

}