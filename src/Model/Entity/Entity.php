<?php
namespace VPFramework\Model\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;
use VPFramework\Form\Annotations\EnumField;
use VPFramework\Form\Annotations\FileField;
use VPFramework\Form\Annotations\NumberField;
use VPFramework\Form\Annotations\PasswordField;
use VPFramework\Utils\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use VPFramework\Form\Annotations\Field;
use VPFramework\Form\Annotations\IgnoredField;
use VPFramework\Form\Annotations\RelationField;
use VPFramework\Form\Annotations\TextLineField;
use VPFramework\Service\Admin\Annotations\ForFilter;
use VPFramework\Service\Admin\Annotations\ShowInList;
use VPFramework\Utils\FlexibleClassTrait;

/**
 * @author VPOREL-DEV
 *
 */
abstract class Entity {
    use FlexibleClassTrait;

    /**
     * La propriété de l'entité qui est utilisée comme clée primaire
     * Les clés composites ne sont pas gérées par le framework
     */
    public abstract function getKeyProperty():string;

    /**
     * Le champ utilisé naturellement pour ordoner les éléments de l'entité
     * Pour un ordre décroissant il faut ajouter un tiret devant le nom de l'entité
     */
    public function getNaturalOrderField():string
    {
        return $this->getKeyProperty();
    }

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
                throw new EntityException("La Classe $entityClass n'est pas une sous classe de la classe Entity");
            }
        }else{
            throw new EntityException("Classe $entityClass non instanciable");
        }
    }

    public static function getEntityNaturalOrderField(string $entityClass){
        $reflectedClass = new \ReflectionClass($entityClass);

        if ($reflectedClass->isInstantiable()) {
            $constructor = $reflectedClass->getConstructor();
            if ($constructor !== null) {
                $object = $reflectedClass->newInstanceArgs([]);
            } else {
                $object = new $entityClass();
            }
            if(is_a($object, Entity::class)){
                return $object->getNaturalOrderField();
            }else{
                throw new EntityException("La Classe $entityClass n'est pas une sous classe de la classe Entity");
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
                $field["formAnnotation"] = null;
                $formAnnotation = self::getFormAnnotation($entityClass, $fieldName);
                if($formAnnotation != null){
                    if($formAnnotation["annotation"]->label != "")
                        $field["label"] = $formAnnotation["annotation"]->label;
                    if($formAnnotation["type"] !== null)
                        $field["type"] = $formAnnotation["type"];
                    $field["formAnnotation"] = $formAnnotation["annotation"];
                }
                $field["adminAnnotations"] = self::getAdminAnnotations($entityClass, $fieldName);
                $fields[$field["name"]] = $field;
            }
        }
        foreach($entityMetaData->getAssociationMappings() as $fieldName => $assocMapping){
            if($assocMapping["type"] == ClassMetadataInfo::MANY_TO_ONE || $assocMapping["type"] == ClassMetadataInfo::ONE_TO_ONE){
                $field = [];
                $field["name"] = $fieldName;
                $field["label"] = $fieldName;
                $joinColumnAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $fieldName, ORM\JoinColumn::class);
                if($joinColumnAnnotation != null){
                    $field["nullable"] = $joinColumnAnnotation->nullable;
                    $field["formAnnotation"] = null;
                    $formAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $fieldName, RelationField::class);
                    if($formAnnotation != null){
                        if($formAnnotation->label != "")
                            $field["label"] = $formAnnotation->label;
                        $field["type"] = "RelationField";
                        $field["formAnnotation"] = $formAnnotation;
                    }else{
                        throw new EntityException("La propriété '$fieldName' ne possède pas l'annotation VPFramework\Model\Entity\Annotatios\RelationField");
                    }
                    $field["adminAnnotations"] = self::getAdminAnnotations($entityClass, $fieldName);
                    $fields[$field["name"]] = $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Retourne l'objet annotation du formulaire du le champ
     * Si plusieurs annoations ont été définies, seule la première sera renvoyée
     * @return null|Object
     */
    public static function getFormAnnotation(string $entityClass, string $property){
        $formAnnotation = null;
        $formAnnotationsClasses = [
            IgnoredField::class, //Première annotation recherchée car si elle est présente les autres ne sont pas prises en compte
            FileField::class, EnumField::class, NumberField::class,
            PasswordField::class, TextLineField::class
        ];
        foreach($formAnnotationsClasses as $formAnnotationClass){
            $formAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $property, $formAnnotationClass);
            if($formAnnotation != null){
                $classNameParts = explode("\\", $formAnnotationClass);
                return ["type" => end($classNameParts), "annotation" => $formAnnotation];
            }
        }
        $formAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $property, Field::class);
        if($formAnnotation != null){
            return ["type" => null, "annotation" => $formAnnotation];
        }
        return null;
    }

    /**
     * Get from the field, the admin annotations defined
     * @return array
     */
    public static function getAdminAnnotations(string $entityClass, string $property):array
    {
        $adminAnnotations = [];
        $adminAnnotationsClasses = [
            ShowInList::class, ForFilter::class
        ];
        foreach($adminAnnotationsClasses as $adminAnnotationClass){
            $adminAnnotation = AnnotationReader::getPropertyAnnotation($entityClass, $property, $adminAnnotationClass);
            if($adminAnnotation != null){
                $adminAnnotations[] = $adminAnnotation;
            }
        }
        return $adminAnnotations;
    }
}
