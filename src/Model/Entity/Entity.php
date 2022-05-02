<?php
namespace VPFramework\Model\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Annotations\EnumField;
use VPFramework\Model\Entity\Annotations\FileField;
use VPFramework\Model\Entity\Annotations\NumberField;
use VPFramework\Model\Entity\Annotations\PasswordField;
use VPFramework\Utils\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ReflectionClass;
use VPFramework\Model\Entity\Annotations\RelationField;
use VPFramework\Utils\FlexibleClassTrait;

/**
 * @author VPOREL-DEV
 *
 */
abstract class Entity {
    use FlexibleClassTrait;

    public abstract function getKeyProperty();

    public static function getEntityKeyProperty(string $entityClass){
        $reflectedClass = new \ReflectionClass($entityClass);

        if ($reflectedClass->isInstantiable()) {
            $constructor = $reflectedClass->getConstructor();
            if ($constructor !== null) {
                $object = $reflectedClass->newInstanceArgs([]);
            } else {
                $object = new $entityClass();
            }
            if(is_a($object, Entity::class)){
                return $object->getKeyProperty();
            }else{
                throw new EntityException("La Classe $entityClass n'est pas une sous classe d ela classe Entity");
            }
        }else{
            throw new EntityException("Classe $entityClass non instanciable");
        }
    }

    /**
     * Retourne les champs de la classe entité en paramètre
     * avec des informations comme le nom, le label, le type(selon doctrine et VpFramework), le caractère nullable, 
     * l'objet annotation si une annoation de vpframework a été utilisée)
     * 
     * @param string $entityClass
     */
    public static function getFields(string $entityClass)
    {
        $entityManager = DIC::getInstance()->get(EntityManager::class);
		$entityMetaData = $entityManager->getClassMetaData($entityClass);
        $fields = [];
        foreach($entityMetaData->getFieldNames() as $fieldName){
            if($fieldName != self::getEntityKeyProperty($entityClass)){
                //LE champ clé primaire n'est pas retourné dans la liste
                $field = [];
                $field["name"] = $fieldName;
                $field["label"] = $fieldName;
                $field["type"] = $entityMetaData->getTypeOfField($fieldName);
                $field["nullable"] = $entityMetaData->isNullable($fieldName);
                $field["VPFAnnotation"] = null;
                $VPFFieldAnnotation = self::getVPFFieldAnnotation($entityClass, $fieldName);
                if($VPFFieldAnnotation != null){
                    if($VPFFieldAnnotation["annotation"]->label != "")
                        $field["label"] = $VPFFieldAnnotation["annotation"]->label;
                    if($VPFFieldAnnotation["type"] !== null)
                        $field["type"] = $VPFFieldAnnotation["type"];
                    $field["VPFAnnotation"] = $VPFFieldAnnotation["annotation"];
                }
                $fields[$field["name"]] = $field;
            }
        }
        foreach($entityMetaData->getAssociationMappings() as $fieldName => $assocMapping){
            if($assocMapping["type"] == ClassMetadataInfo::MANY_TO_ONE || $assocMapping["type"] == ClassMetadataInfo::ONE_TO_ONE){
                $field = [];
                $field["name"] = $fieldName;
                $field["label"] = $fieldName;
                $field["type"] = null;
                $joinColumnAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $fieldName, ORM\JoinColumn::class);
                $field["nullable"] = $joinColumnAnnotation->nullable;
                $field["VPFAnnotation"] = null;
                $VPFFieldAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $fieldName, RelationField::class);
                if($VPFFieldAnnotation != null){
                    if($VPFFieldAnnotation->label != "")
                        $field["label"] = $VPFFieldAnnotation->label;
                    $field["type"] = "RelationField";
                    $field["VPFAnnotation"] = $VPFFieldAnnotation;
                }else{
                    throw new EntityException("La propriété '$fieldName' ne possède pas l'annotation VPFramework\Model\Entity\Annotatios\RelationField");
                }
                $fields[$field["name"]] = $field;
            }
        }
        return $fields;
    }

    /**
     * Retourne l'objet annotation de VPFramework sur le champ
     * VPF = VPFramework
     * @return null|Object
     */
    public static function getVPFFieldAnnotation(string $entityClass, string $property){
        $VPFFieldAnnotation = null;
        $VPFFieldsClasses = [FileField::class, EnumField::class, NumberField::class,PasswordField::class, TextLineField::class];
        foreach($VPFFieldsClasses as $VPFFieldClass){
            $VPFFieldAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $property, $VPFFieldClass);
            if($VPFFieldAnnotation != null){
                $classNameParts = explode("\\", $VPFFieldClass);
                return ["type" => end($classNameParts), "annotation" => $VPFFieldAnnotation];
            }
        }
        $VPFFieldAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $property, Field::class);
        if($VPFFieldAnnotation != null){
            return ["type" => null, "annotation" => $VPFFieldAnnotation];
        }
        return null;
    }
}
