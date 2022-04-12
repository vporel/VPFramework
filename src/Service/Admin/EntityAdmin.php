<?php
namespace VPFramework\Service\Admin;

use Doctrine\ORM\EntityManager;
class EntityAdmin
{  
    private $entityClass, $repositoryClass, $mainFields;  
    /**
     * __construct
     * 
     * !!! Important : L'entité doit redéfinir la méthode __toString()
     *
     * @param string $entityClass Ex : User::class
     * @param string $repositoryClass Ex : UserRepository::class
     * @param array $mainFields La liste des champs qui seront affichés lorsqu'on présentera la liste des éléments
     * @return void
     */
    public function __construct(string $entityClass, string $repositoryClass, array $mainFields = [])
    {
        $this->entityClass = $entityClass;
        $this->repositoryClass = $repositoryClass;
        $this->mainFields = $mainFields;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

    public function getRepositoryClass(){
        return $this->repositoryClass;
    }

    public function getMainFields(){
        return $this->mainFields;
    }

    /**
     * @return string La classe entité sans le namespace
     */
    public function getName(){
        $classNameParts = explode("\\", $this->entityClass);
        return end($classNameParts);
    }

    /**
     * @return array Tableau associant chaque propriété de l'attribut à son type dans doctrine (Ex : name => string)
     */
    public function getFields(EntityManager $entityManager){
        
		$entityMetaData = $entityManager->getClassMetaData($this->getEntityClass());
        $fields = [];
        foreach($entityMetaData->getFieldNames() as $fieldName){
            $fields[$fieldName] = $entityMetaData->getTypeOfField($fieldName);
        }
        return $fields;
    }

    public function getMetaData(EntityManager $entityManager){
        return $entityManager->getClassMetaData($this->getEntityClass());
    }
}