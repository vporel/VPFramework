<?php
namespace VPFramework\Service\Admin;

use Doctrine\ORM\EntityManager;
use VPFramework\Model\Entity\Annotations\Field;
use VPFramework\Model\Entity\Annotations\FileField;
use VPFramework\Model\Entity\Annotations\RelationField;
use VPFramework\Utils\AnnotationReader as UtilsAnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping as ORM;
use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Annotations\EnumField;
use VPFramework\Model\Entity\Annotations\NumberField;
use VPFramework\Model\Entity\Annotations\PasswordField;
use VPFramework\Model\Entity\Annotations\TextLineField;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;
use VPFramework\Utils\FlexibleClassTrait;

class EntityAdmin
{  
    use FlexibleClassTrait;

    private $entityClass, $repositoryClass, $mainFieldsNames;

    private $filterFieldsNames;  
    /**
     * __construct
     * 
     * !!! Important : L'entité doit redéfinir la méthode __toString()
     *
     * @param string $repositoryClass Ex : UserRepository::class
     * @param array $mainFieldsNames La liste des champs qui seront affichés lorsqu'on présentera la liste des éléments
     * @param array $filterFieldsNames La liste des champs qui seront utilisés comme critère pour le filtre
     * Les éléments du filterFieldsNames qui sont du type text (pour textarea) ne seront pas pris en compte
     * Si le paramètre $mainFields n'est pas vide, les éléments du $filterFieldsNames qui n'y figurent pas ne seront pas pris en compte
     * @return void
     */
    public function __construct(string $repositoryClass, array $mainFieldsNames = [], array $filterFieldsNames = [])
    {
        $this->repositoryClass = $repositoryClass;
        $this->mainFieldsNames = $mainFieldsNames;
        $this->filterFieldsNames = $filterFieldsNames;
        $this->entityClass = Repository::getRepositoryEntityClass($this->repositoryClass);
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

    public function getRepositoryClass(){
        return $this->repositoryClass;
    }

    /**
     * @return array
     */
    public function getMainFields(){
        $fields = [];
        foreach($this->getFields() as $fieldName => $field){
            if(in_array($fieldName, $this->mainFieldsNames))
                $fields[$fieldName] = $field;
        }
		return (count($fields) > 0) ? $fields : $this->getFields();
    }

    /**
     * Retourne les champs utilisés pour le filtre, 
     * Il s'agira des éléments de la propriété mainFieldsNames qui apparaisent dans la propriété filterFieldsNames
     * @return array
     */
    public function getFilterFields(){
        $fields = [];
        foreach($this->getMainFields() as $fieldName => $field){
            if(in_array($fieldName, $this->filterFieldsNames))
                $fields[$fieldName] = $field;
        }
		return $fields;
    }

    /**
     * @return string La classe entité sans le namespace
     */
    public function getName(){
        $classNameParts = explode("\\", $this->entityClass);
        return end($classNameParts);
    }

    /**
     * Retourne les noms des champs de l
     * @return array Tableau associant chaque propriété de l'attribut à son type dans doctrine (Ex : name => string)
     */
    public function getFields(){
        return Entity::getFields($this->entityClass);
    }

    /**
     * Vérifie si l'entité courant provient du framework
     */
    public function isBuiltin(){
        return in_array($this->getName(), ["Admin", "AdminGroup", "AdminGroupPermission"]);
    }


}