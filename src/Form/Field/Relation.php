<?php

namespace VPFramework\Form\Field;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;
use VPFramework\Utils\ClassUtil;
use VPFramework\Utils\ObjectReflection;

/**
 * Cette classe n'est pas utilisable dans un contexte différent de celui de VPFramework et de Doctrine
 * Car elle ne peut fonctionner sans les classes : Doctrine\ORM\EntityManager et VPFramework\Core\DIC;.
 */
class Relation extends Select
{

    private $entityClass, $keyProperty;
    /**
     * @var string
     */
    private $repositoryClass = null;
    /**
     * @var string
     */
    private $linkToAdd = null;

    /**
     * @var array
     */
    private $objects;
    
    /**
     * @param string $label
     * @param string $name
     * @param string $repositoryClass
     * @param array $elements
     */
    public function __construct(string $label, string $name, string $repositoryClass, array $objects)
    {
        $this->repositoryClass = $repositoryClass;
        $this->entityClass = Repository::getRepositoryEntityClass($this->repositoryClass);
        $this->keyProperty = Entity::getEntityKeyProperty($this->entityClass);
        $this->objects = $objects;
        $selectElements = [];
        foreach($this->objects as $object){
            $selectElements[ObjectReflection::getPropertyValue($object, $this->getKeyProperty())] = (string) $object;
        }
        parent::__construct($label, $name, $selectElements);
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function getKeyProperty()
    {
        return $this->keyProperty;
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
        foreach ($this->objects as $element) {
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

        $value = $value ?? $this->getDefaultValue();
        if(is_object($value))
            $value = ObjectReflection::getPropertyValue($value, $this->getKeyProperty());
        if($this->isReadOnly()){
            $textToShow = "";
            foreach ($this->objects as $element) {
                $elementValue = ObjectReflection::getPropertyValue($element, $this->getKeyProperty());
                if($elementValue == $value){
                    $textToShow = (string) $element;
                    break;
                }
            }
            return "<span class='form-read-only-value'>$textToShow</span>";
        }else{
            $html = '
                    <select name="'.$this->name.'" id="'.$this->name.'">
            ';
            $html .= '<option value="">Aucun</option>';
            foreach ($this->objects as $element) {
                $elementValue = ObjectReflection::getPropertyValue($element, $this->getKeyProperty());
                $html .= '<option value="'.$elementValue.'" '.($elementValue == $value ? 'selected' : '').'>'. (string) $element.'</option>';
            }

            $html .= '</select>';
            $entityName = ClassUtil::getSimpleName($this->getEntityClass());
            if($this->getLinkToAdd() != null){
                $html .= '<button type="button" class="reload-related-elements-btn" id="'.$this->name.'-reload" onclick="reload_'.$this->name.'_'.$entityName.'()">Recharger la liste</button>';
                $html .= '<a class="add-related-element-link" href="'.$this->getLinkToAdd().'" target="_blank">Ajouter</a>';
                $html .= '
                    <script type="text/javascript">
                        function reload_'.$this->name.'_'.$entityName.'(){
                            var xhttp = new XMLHttpRequest();
                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    var elements = JSON.parse(this.responseText);
                                    var newSelectCode = "<option value=\'\'>Aucun</option>";
                                    for(element of elements){
                                        newSelectCode += "<option value=\'"+element["value"]+"\'>"+element["text"]+"</option>";
                                    }
                                    document.getElementById("'.$this->name.'").innerHTML = newSelectCode;
                                }
                            };
                            xhttp.open("GET", "/admin/'.$entityName.'/jsonList", true);
                            xhttp.send();
                        }
                    </script>
                ';
            }
            return $html;
        }
    }

    public function getRealValue($value)
    {
        $element = null;
        foreach($this->objects as $el){
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

    /**
     * Get the value of repositoryClass
     *
     * @return  string
     */ 
    public function getRepositoryClass()
    {
        return $this->repositoryClass;
    }

    /**
     * Set the value of repositoryClass
     *
     * @param  string  $repositoryClass
     *
     * @return  self
     */ 
    public function setRepositoryClass(string $repositoryClass)
    {
        $this->repositoryClass = $repositoryClass;

        return $this;
    }

    /**
     * Get the value of linkToAdd
     *
     * @return  string|null
     */ 
    public function getLinkToAdd()
    {
        return $this->linkToAdd;
    }

    /**
     * Set the value of linkToAdd
     *
     * @param  string  $linkToAdd
     *
     * @return  self
     */ 
    public function setLinkToAdd(string|null $linkToAdd)
    {
        $this->linkToAdd = $linkToAdd;

        return $this;
    }
}
