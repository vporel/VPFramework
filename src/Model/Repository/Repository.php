<?php

namespace VPFramework\Model\Repository;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Types\Types;
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
     * @param array $criteria Les critères seront tous passés avec l'opération AND, pour un filtre plus poussé, créer une méthode personnalisée
     * @return Query
     */
    private function buildQuery(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from($this->getEntityClass(), "e");

        //Criteria
        $alreadyHasWhereClose = false;
        foreach($criteria as $key => $value){
            $dividedKey = explode("__", $key);
            $realKey = $dividedKey[0];
            if(!$alreadyHasWhereClose){
                $whereMethod = "where";
                $alreadyHasWhereClose = true;
            }else{
                $whereMethod = "andWhere";
            }
            if(count($dividedKey) < 2){ //Pas de paramètre particulier : recherche d'une égalité
                if(!$alreadyHasWhereClose)
                    $queryBuilder->$whereMethod("e.".$realKey." = :".$realKey);
            }else{
                switch (strtolower($dividedKey[1])){
                    case "neq": $queryBuilder->$whereMethod("e.".$realKey." != :".$realKey); break;
                    case "gt": $queryBuilder->$whereMethod("e.".$realKey." > :".$realKey); break;
                    case "gte": $queryBuilder->$whereMethod("e.".$realKey." >= :".$realKey); break;
                    case "lt": $queryBuilder->$whereMethod("e.".$realKey." < :".$realKey); break;
                    case "lte": $queryBuilder->$whereMethod("e.".$realKey." <= :".$realKey); break;
                    case "like": $queryBuilder->$whereMethod("e.".$realKey." LIKE :".$realKey); break;
                    default: $queryBuilder->$whereMethod("e.".$realKey." = :".$realKey);break;
                }
            }
            if($value instanceof DateTime)
                $queryBuilder->setParameter($realKey, $value, Types::DATE_IMMUTABLE);
            else
                $queryBuilder->setParameter($realKey, $value);
        }
        //Order
        if($orderBy != null && is_array($orderBy)){
            foreach($orderBy as $order){
                if(strpos($order, "-") != false && strpos($order, "-") == 0){
                    $queryBuilder->orderBy("e.".substr($order, 1), "DESC");
                }else{
                    $queryBuilder->orderBy("e.".$order, "ASC");
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
        }catch(\Doctrine\DBAL\Exception $e){
            $PDOException = $e->getPrevious();
            $this->managePDOException($PDOException);
            return null;
        }
    }

    public function findAll()
    {
        try{
            return parent::findAll();
        }catch(\Doctrine\DBAL\Exception $e){
			$PDOException = $e->getPrevious();
			$this->managePDOException($PDOException);
            return [];
		}
    }

    /**
     * @return void
     */
    private function managePDOException($exception){
        switch($exception->getCode()){
            case Repository::PDO_EXCEPTION_TABLE_NOT_FOUND: $this->createTable();break;
        }
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
}