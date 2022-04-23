<?php
namespace VPFramework\Service\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionProperty;
use VPFramework\Model\Entity\Annotations\Field;
use VPFramework\Model\Entity\Annotations\FileField;
use VPFramework\Model\Entity\Annotations\RelationField;
use VPFramework\Utils\AnnotationReader as UtilsAnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping as ORM;
use VPFramework\Model\Entity\Annotations\EnumField;
use VPFramework\Model\Entity\Annotations\NumberField;
use VPFramework\Model\Entity\Annotations\PasswordField;
use VPFramework\Model\Entity\Annotations\TextLineField;
use VPFramework\Utils\FlexibleClassTrait;

class EntityAdmin
{  
    use FlexibleClassTrait;

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
     * Retourne un tableau à 2 dimensions
     * [
     *  field : [
     *      name:string
     *      label:string
     *      type:string
     *      nullable:bool
     *      customAnnotation:Object
     *  ]
     * ]
     * @return array Tableau associant chaque propriété de l'attribut à son type dans doctrine (Ex : name => string)
     */
    public function getFields(EntityManager $entityManager){
        
		$entityMetaData = $entityManager->getClassMetaData($this->getEntityClass());
        $fields = [];
        foreach($entityMetaData->getFieldNames() as $fieldName){
            $field = [];
            $field["name"] = $fieldName;
            $field["label"] = $fieldName;
            $field["type"] = $entityMetaData->getTypeOfField($fieldName);
            $field["nullable"] = $entityMetaData->isNullable($fieldName);
            $field["customAnnotation"] = null;
            $customFieldAnnotation = $this->getCustomFieldAnnotation($fieldName);
            if($customFieldAnnotation != null){
                if($customFieldAnnotation["annotation"]->label != "")
                    $field["label"] = $customFieldAnnotation["annotation"]->label;
                if($customFieldAnnotation["type"] !== null)
                    $field["type"] = $customFieldAnnotation["type"];
                $field["customAnnotation"] = $customFieldAnnotation["annotation"];
            }
            $fields[] = $field;
        }
        foreach($entityMetaData->getAssociationMappings() as $fieldName => $assocMapping){
            if($assocMapping["type"] == ClassMetadataInfo::MANY_TO_ONE || $assocMapping["type"] == ClassMetadataInfo::ONE_TO_ONE){
                $field = [];
                $field["name"] = $fieldName;
                $field["label"] = $fieldName;
                $field["type"] = null;
                $joinColumnAnnotation = UtilsAnnotationReader::getPropertyAnnotation($this->entityClass, $fieldName, ORM\JoinColumn::class);
                $field["nullable"] = $joinColumnAnnotation->nullable;
                $field["customAnnotation"] = null;
                $customFieldAnnotation = UtilsAnnotationReader::getPropertyAnnotation($this->entityClass, $fieldName, RelationField::class);
                if($customFieldAnnotation != null){
                    if($customFieldAnnotation->label != "")
                        $field["label"] = $customFieldAnnotation->label;
                    $field["type"] = "RelationField";
                    $field["customAnnotation"] = $customFieldAnnotation;
                }else{
                    throw new EntityAdminException("La propriété '$fieldName' ne possède pas l'annotation VPFramework\Model\Entity\Annotatios\RelationField");
                }
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function getMetaData(EntityManager $entityManager){
        return $entityManager->getClassMetaData($this->getEntityClass());
    }

    public function isBuiltin(){
        return in_array($this->getName(), ["Admin", "AdminGroup", "AdminGroupPermission"]);
    }

    private function getCustomFieldAnnotation($property){
        $customFieldAnnotation = null;
        $customFieldsClasses = [FileField::class, EnumField::class, NumberField::class,PasswordField::class, TextLineField::class];
        foreach($customFieldsClasses as $customFieldClass){
            $customFieldAnnotation = UtilsAnnotationReader::getPropertyAnnotation($this->entityClass, $property, $customFieldClass);
            if($customFieldAnnotation != null){
                $classNameParts = explode("\\", $customFieldClass);
                return ["type" => end($classNameParts), "annotation" => $customFieldAnnotation];
            }
        }
        $customFieldAnnotation = UtilsAnnotationReader::getPropertyAnnotation($this->entityClass, $property, Field::class);
        if($customFieldAnnotation != null){
            return ["type" => null, "annotation" => $customFieldAnnotation];
        }
        return null;
    }

}