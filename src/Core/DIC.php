<?php

namespace VPFramework\Core;

use VPFramework\Core\Configuration\AppConfiguration;
use VPFramework\Model\Doctrine\Config;
use Doctrine\ORM\EntityManager;
use ReflectionClass;

/**
 * Conteneur d'injection de dépendances dans l'application.
 */
class DIC
{
    private $dependances = [];
    private $instances = [];
    private static $instance = null;

    private function __construct()
    {
        //Méthode connexion à la base de données
        $createEntityManager = function(){
            $database = $this->get(AppConfiguration::class)->get('database');

            // database configuration parameters
            $conn = [
                'driver' => 'pdo_mysql',
                'user' => $database['username'],
                'password' => $database['password'],
                'dbname' => $database['name'],
                'host' => $database['host']
            ];
            return EntityManager::create($conn, Config::getConfig());
        };

        $this->addDependance(EntityManager::class, $createEntityManager);
        $this->addDependance(EntityManagerInterface::class, $createEntityManager);
    }

    public function get($name, array $paramsValues = [])
    {
        if (!array_key_exists($name, $this->instances)) {
            if (array_key_exists($name, $this->dependances)) {
                    $callable = $this->dependances[$name];
                    $this->instances[$name] = $callable();
            } else {
                $this->dynamicInstanciation($name, $paramsValues);
            }
        }

        return $this->instances[$name];
    }

    /**
     * Méthode permettant de définir comment une classe particulière sera instanciée
     * @param string $name Nom de la classe
     * @param callable $resolver La fonction qui fait l'instanciation. Elle ne sera appelée que lors de la première demande de l'instance 
     * @return void
     */
    public function addDependance(string $name, callable $resolver)
    {
        $this->dependances[$name] = $resolver;
    }

    /**
     * Méthode permettant de définir l'instance utilisée pour une classe
     * @param string $name Nom de la classe
     * @param object $instance Instance
     * @return void
     */
    public function addInstance(string $name, $instance)
    {
        $this->instances[$name] = $instance;
    }

    public function invoke($obj, $method, array $paramsValues = [])
    {
        $reflectedClass = new \ReflectionClass($obj);
        $reflectedMethod = $reflectedClass->getMethod($method);
        if ($reflectedMethod) {
            return $obj->$method(...$this->getParametersValues($reflectedMethod, $paramsValues));
        } else {
            throw new \Exception("La méthode $method n'existe pas dans la classe ".$obj->getClass());
        }
    }

    public function invokeStatic($class, $method, array $paramsValues = [])
    {
        $reflectedClass = new \ReflectionClass($class);
        $reflectedMethod = $reflectedClass->getMethod($method);
        if ($reflectedMethod) {

            return $class::$method(...$this->getParametersValues($reflectedMethod, $paramsValues));
        } else {
            throw new \Exception("La méthode $method n'existe pas dans la classe ".$class);
        }
    }

    private function dynamicInstanciation($class, $paramsValues = [])
    {
        $reflectedClass = new \ReflectionClass($class);

        if ($reflectedClass->isInstantiable()) {
            $constructor = $reflectedClass->getConstructor();
            if ($constructor !== null) {
                $this->instances[$class] = $reflectedClass->newInstanceArgs($this->getParametersValues($constructor, $paramsValues));
            } else {
                $this->instances[$class] = new $class();
            }
        }
    }

    /**
     * Retourne les parametres à passer à une méthode ou un constructeur
     * @param Constructor|Method $element
     * @param array $paramValues LEs valeurs "PAr défaut"
     * @return array
     */
    private function getParametersValues($element, $values = []){
        $parameters = $element->getParameters();
        $parametersValues = [];
        foreach ($parameters as $param) {
            if (!array_key_exists($param->getName(), $values)) {
                if ($param->getType() && !$param->getType()->isBuiltin()) {
                    $parametersValues[] = $this->get($param->getClass()->getName());
                } else {
                    $parametersValues[] = $param->getDefaultValue();
                }
            } else {
                $parametersValues[] = $values[$param->getName()];
            }
        }
        return $parametersValues;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DIC();
        }

        return self::$instance;
    }
}
