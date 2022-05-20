<?php
namespace VPFramework\Service\Admin;

use Doctrine\ORM\EntityManager;
use VPFramework\Form\Annotations\Field;
use VPFramework\Form\Annotations\FileField;
use VPFramework\Form\Annotations\RelationField;
use VPFramework\Utils\AnnotationReader as UtilsAnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping as ORM;
use VPFramework\Core\DIC;
use VPFramework\Form\Annotations\EnumField;
use VPFramework\Form\Annotations\NumberField;
use VPFramework\Form\Annotations\PasswordField;
use VPFramework\Form\Annotations\TextLineField;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;
use VPFramework\Service\Admin\Annotations\{ShowInList, ForFilter};
use VPFramework\Utils\FlexibleClassTrait;

class EntityAdmin
{  
    use FlexibleClassTrait;

    private $entityClass, $repositoryClass;
    /**
     * __construct
     * 
     * !!! Important : L'entité doit redéfinir la méthode __toString()
     *
     * @param string $repositoryClass Ex : UserRepository::class
     * Les éléments du filterFieldsNames qui sont du type text (pour textarea) ne seront pas pris en compte
     * Si le paramètre $mainFields n'est pas vide, les éléments du $filterFieldsNames qui n'y figurent pas ne seront pas pris en compte
     * @return void
     */
    public function __construct(string $repositoryClass)
    {
        $this->repositoryClass = $repositoryClass;
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
            foreach($field["adminAnnotations"] as $adminAnnotation){
                if($adminAnnotation instanceof ShowInList){
                    $fields[$fieldName] = $field;
                    continue;
                }
            }
        }
		return (count($fields) > 0) ? $fields : $this->getFields();
    }

    /**
     * Retourne les champs utilisés pour le filtre, 
     * Il s'agira des propriétés qui ont à la fois l'annotation ForFilter
     * @return array
     */
    public function getFilterFields(){
        $fields = [];
        foreach($this->getFields() as $fieldName => $field){
            foreach($field["adminAnnotations"] as $adminAnnotation){
                if($adminAnnotation instanceof ForFilter){
                    $fields[$fieldName] = $field;
                    continue;
                }
            }
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
        $fields = [];
        foreach(Entity::getFields($this->entityClass) as $fieldName => $field){
            $fields[$fieldName] = $field;
        }
        return $fields;
    }

    /**
     * Vérifie si l'entité courant provient du framework
     */
    public function isBuiltin(){
        return in_array($this->getName(), ["Admin", "AdminGroup", "AdminGroupPermission"]);
    }


}