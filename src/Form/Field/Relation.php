<?php

namespace VPFramework\Form\Field;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;
use VPFramework\Utils\ObjectReflection;

/**
 * Cette classe n'est pas utilisable dans un contexte différent de celui de VPFramework et de Doctrine
 * Car elle ne peut fonctionner sans les classes : Doctrine\ORM\EntityManager et VPFramework\Core\DIC;.
 */
class Relation extends Select
{

    private $entityClass, $keyProperty;

    public function __construct($label, $name, $options = [])
    {
        $this->addOption('repositoryClass', null);
        parent::__construct($label, $name, $options);
        $this->entityClass = Repository::getRepositoryEntityClass($this->getRepositoryClass());
        $this->keyProperty = Entity::getEntityKeyProperty($this->entityClass);
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function getKeyProperty()
    {
        return $this->keyProperty;
    }

    public function getRepositoryClass()
    {
        if ($this->options['repositoryClass'] !== null) {
            return $this->options['repositoryClass'];
        } else {
            throw new \Exception('Aucune classe Repository passée dans les options pour la relation');
        }
    }

    /**
     * Ne retourne pas un tableau d'objets tels que passés dans les options
     * Retourne un tableau associatif
     * Ce tableau associe la valeur de la propriété 'keyProperty' des objets à la chaine correspondante via la méthode toString
     */
    public function getElements()
    {
        $array = [];
        foreach($this->options["elements"] as $element){
            $array[ObjectReflection::getPropertyValue($element, $this->getKeyProperty())] = (string) $element;
        }
        return $array;
    }

    public function getElementsAndAssociationsFields()
    {
        $array = [];
        if ($this->isNullable()) {
            $array[] = ['value' => '', 'text' => 'Aucun'];
        }
        $metadata = DIC::getInstance()->get(EntityManager::class)->getClassMetadata($this->getEntityClass());
        $associationFields = [];
        foreach ($metadata->associationMappings as $field) {
            if (isset($field['joinColumns'])) {
                $associationFields[] = $field['fieldName'];
            }
        }
        foreach ($this->options["elements"] as $element) {
            $option = [
                'value' => ObjectReflection::getPropertyValue($element, $this->getKeyProperty()),
                'text' => (string) $element
            ];
            foreach ($associationFields as $field) {
                $linkedObject = ObjectReflection::getPropertyValue($element, $field);
                $option[$field] = ($linkedObject != null) ? ObjectReflection::getPropertyValue($linkedObject, Entity::getEntityKeyProperty(get_class($linkedObject))) : ''; // On passe à chaque element les identifiants des champs associés pour ces éléménts
            }
            $array[] = $option;
        }

        return ['associationFields' => $associationFields, 'elements' => $array];
    }

    protected function getCustomHTML($value)
    {

        $value = $value ?? $this->getDefault();
        if(is_object($value))
            $value = ObjectReflection::getPropertyValue($value, $this->getKeyProperty());
        if($this->isReadOnly()){
            $textToShow = "";
            foreach ($this->options["elements"] as $element) {
                $elementValue = ObjectReflection::getPropertyValue($element, $this->getKeyProperty());
                if($elementValue == $value){
                    $textToShow = (string) $element;
                    break;
                }
            }
            return "<span class='form-read-only-value'>$textToShow</span>";
        }else{
            $select = '
                    <select name="'.$this->name.'" id="'.$this->name.'" '.$this->getReadOnlyText().'>
            ';
            foreach ($this->options["elements"] as $element) {
                $elementValue = ObjectReflection::getPropertyValue($element, $this->getKeyProperty());
                $select .= '<option value="'.$elementValue.'" '.($elementValue == $value ? 'selected' : '').'>'. (string) $element.'</option>';
            }

            $select .= '</select>';

            return $select;
        }
    }

    public function getRealValue($value)
    {
        $element = null;
        foreach($this->options["elements"] as $el){
            if(ObjectReflection::getPropertyValue($el, $this->getKeyProperty()) == $value){
                $element = $el;
            }
        }
        if ($element == null & !$this->isNullable()) {
            throw new \Exception($this->getName()." : La valeur $value ne correspond à aucun élément. Ou alors définissez ce champ comme nullable");
        } 
        return $element;
    }

    public function serialize()
    {
        $elementsAndAssoc = $this->getElementsAndAssociationsFields();

        return array_merge(parent::serialize(), [
            'elements' => $elementsAndAssoc['elements'],
            'associationFields' => $elementsAndAssoc['associationFields'],
        ]);
    }
}
