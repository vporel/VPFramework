<?php

namespace VPFramework\Model\Repository;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Parameter;
use VPFramework\Core\DIC;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Classe repository
 * Les méthodes (findBy, findOneBy) suivent le modèle du framework
 */
abstract class Repository extends EntityRepository
{
    //Codes de quelques exceptions pouvant être lancées par PDO
    const PDO_EXCEPTION_TABLE_NOT_FOUND = "42S02";

    public function __construct()
    {
        parent::__construct(DIC::getInstance()->get(EntityManager::class), new ClassMetadata($this->getEntityClass()));
        
    }

    /**
     * Retourne la classe entité à laquelle le repository est lié
     * Ex :
     *      return Entity::class
     */
    public abstract function getEntityClass();

    /**
     * @param string $propertyName
     * @param string $operatorAlias Ex : neq, gt, gte
     * 
     * @return string
     */
    final protected function getConditionText(string $propertyName, string $operatorAlias):string
    {
        $text = "entity.".$propertyName;
        switch (strtolower($operatorAlias)){
            case "neq": $text .= " != "; break;
            case "gt": $text .=" > "; break;
            case "gte": $text .=" >= "; break;
            case "lt": $text .=" < "; break;
            case "lte": $text .=" <= "; break;
            case "like": $text .=" LIKE "; break;
            default: $text .=" = ";break;
        }
        $text .= ":".$propertyName;
        return $text;
    }

    /**
     * Create the condition with with the given criteria
     * @param QueryBuilder $queryBuilder
     * @param array $criteria
     * 
     * @return void
     */
    protected function createCondition(array $criteria, ArrayCollection &$parameters = null):string
    {
        $parameters = new ArrayCollection();
        $conditionTexts = [];
        $nonArrayElements = 0; //The elements in criteria which are not arrays
        foreach($criteria as $key => $element){
            if(!is_array($element)){
                $nonArrayElements++;
                $dividedKey = explode("__", $key);
                $propertyName = $dividedKey[0];
                $operatorAlias = $dividedKey[1] ?? "eq";
                $conditionTexts[] = $this->getConditionText($propertyName, $operatorAlias);
                if($element instanceof DateTime)
                    $parameters[] = new Parameter($propertyName, $element, Types::DATE_IMMUTABLE);
                else
                    $parameters[] = new Parameter($propertyName, $element);
            }else{
                $conditionTexts[] = $this->createCondition($element, $params);
                foreach($params as $param)
                    $parameters[] = $param;
            }
        }
        $operator = ($nonArrayElements > 0) ? "AND" :" OR "; // If there is at least one element which are not an array, we use AND
        return count($conditionTexts) > 0 ? "(".implode(" $operator ", $conditionTexts).")" : "";
    }

    /**
     * @param array $criteria Le passage des crit_res respectes des conventions précises
     * La tableau est à deux dimensions
     * Tous les éléments d'un sous tableau sont associés avec l'opérateur AND
     * Tous les sous tableaux  sont associés avec l'opérateur OR
     * 
     * Pour des critèes plus poussés, créer un méthode personnalisée
     * @return Query
     */
    private function buildQuery(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("entity")
            ->from($this->getEntityClass(), "entity");
        //Criteria
        $condition = $this->createCondition($criteria, $parameters);
        if($condition != ""){
            $queryBuilder->where($condition);
            $queryBuilder->setParameters($parameters);
        }
        //Order
        if($orderBy != null && is_array($orderBy)){
            foreach($orderBy as $order){
                if(substr($order, 0,1) == "-"){
                    $queryBuilder->orderBy("entity.".substr($order, 1), "DESC");
                }else{
                    $queryBuilder->orderBy("entity.".$order, "ASC");
                }
            }
        }

        if(is_int($offset) && $offset >= 0){
            $queryBuilder->setFirstResult($offset);
        }

        if(is_int($limit) && $limit > 0){
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @return Entity[] Un tableau d'instance de la classe entité gérée par le repository
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null){
        try{
            return $this->buildQuery($criteria, $orderBy, $limit, $offset)->getResult();
        }catch(\Doctrine\ORM\NoResultException $e){
            return [];
        }catch(\Doctrine\DBAL\Exception $e){
            $PDOException = $e->getPrevious();
            $this->managePDOException($PDOException);
            return [];
        }
    }

    /**
     * @return Entity|null Une instance de la classe entité gérée par le repository
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        try{
            return $this->buildQuery($criteria, $orderBy, null, null)->getSingleResult();
        }catch(\Doctrine\ORM\NoResultException $e){
            return null;
        }catch(\Doctrine\DBAL\Exception $e){
            $PDOException = $e->getPrevious();
            $this->managePDOException($PDOException);
            return null;
        }catch(\PDOException $e){
            $this->managePDOException($e);
            return null;
        }
    }

    public function exists(array $criteria)
    {
        return $this->findOneBy($criteria, null) != null; 
    }

    public function findAll()
    {
        try{
            return parent::findBy([]);
        }catch(\Doctrine\DBAL\Exception $e){
            $PDOException = $e->getPrevious();
            $this->managePDOException($PDOException);
            return [];
        }catch(\PDOException $e){
            $this->managePDOException($e);
            return [];
        }
    }

    /**
     * @return void
     */
    private function managePDOException($exception){
        if($exception->getCode() == Repository::PDO_EXCEPTION_TABLE_NOT_FOUND || $exception->getCode() == 1146)
            $this->createTable();
    }

    /**
     * @return void
     */
    private function createTable(){
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $metaData = $em->getClassMetaData($this->getEntityClass());
        $tool->createSchema([$metaData]);
    }

    /**
     * Retourne la classe entité gérée par le repository passé en paramètre
     */
    public static function getRepositoryEntityClass(string $repositoryClass)
    {
        $reflectedClass = new \ReflectionClass($repositoryClass);

        if ($reflectedClass->isInstantiable()) {
            $constructor = $reflectedClass->getConstructor();
            if ($constructor !== null) {
                $object = $reflectedClass->newInstanceArgs([]);
            } else {
                $object = new $repositoryClass();
            }

            if(is_a($object, Repository::class)){
                return $object->getEntityClass();
            }else{
                throw new \Exception("La Classe $repositoryClass n'est pas une sous classe de la classe Repository");
            }
        }else{
            throw new EntityException("Classe $entityClass non instanciable");
        }
    }
}