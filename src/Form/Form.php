<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */

namespace VPFramework\Form;

use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\Form\Field;
use VPFramework\Model\Entity\Entity;
use VPFramework\Utils\ObjectReflection;

/**
 * Classe créeant un formulaire qui pourra être affiché dans une page
 * Le formulaire peut est lié à une entité, les champs seront donc ceux de cette classe
 * 
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 */
class Form
{
    /**
     * Nom du formulaire
     * @var string
     */
    protected $name;

    /**
     * Champs du formulaire
     * @var Field[]
     */
    protected $fields;

    /**
     * Tableau associatif donnant des valeurs pour les champs du formulaire
     * @var array
     */
    protected $parameters;
    protected $error = '';

    protected $entityClass;
    /**
     * Objet dont les propriétés sont les valeurs des champs du formulaire
     */
    protected $object = null;

    /**
     * @param string Nom du formulaire
     * @param null|string $entityClass Classe entité à laquelle le formulaire est lié
     * @param Object $object Instance de $entityClass
     * @param string[] $entityFields Champs de l'entité à afficher dans le formulaire
     */
    public function __construct(string $name, string $entityClass = null, Object $object = null, array $fieldsToShow = [])
    {
        $this->name = "form-".$name;
        $this->entityClass = $entityClass;
        $this->parameters = DIC::getInstance()->get(Request::class)->getAll(); // Par défaut, les paramètres sont ceux de la requetes
        if($entityClass != null){
            $this->object = $object;
            $this->fields = self::buildFieldsFromEntity($entityClass, $fieldsToShow);
        }else{
            $this->object = null;
        }
    }

    /**
     * 
     */
    public function setObject(Entity $object)
    {
        $this->object = $object;
    }

    /**
     * Mettre les champs $fieldsNames en mode lecture seule
     */
    public function setReadOnly(array $fieldsNames, bool $readOnly)
    {
        foreach($fieldsNames as $fieldName){
            $this->fields[$fieldName]->setReadOnly($readOnly);
        }
    }

    /**
     * Mettre tout le formulaire en lecture seule
     */
    public function setFormReadOnly(bool $readOnly)
    {
        $this->setReadOnly(array_keys($this->fields), $readOnly);
    }

    /**
     * Creation des champs à partir des propriétés de l"entités
     * @return array<string, AbstractField>
     */
    public static function buildFieldsFromEntity(string $entityClass, array $fieldsToBuild)
    {
        $rawFields = Entity::getFields($entityClass);
        $fields = [];
        foreach($rawFields as $rawField){
            if(count($fieldsToBuild) == 0 || in_array($rawField["name"], $fieldsToBuild)){
                $field = null;
                if(in_array($rawField["type"], ["integer", "smallint", "bigint", "float", "NumberField"])){
                    $field = new Field\Number($rawField["label"],$rawField["name"]);
                    if($rawField["type"] == "NumberField"){
                        $field->setMin($rawField["VPFAnnotation"]->min);
                        $field->setMax($rawField["VPFAnnotation"]->max);
                    }
                }elseif(in_array($rawField["type"], ["date", "date_immutable", "datetime", "datetime_immutable"])){
                    $field = new Field\Date($rawField["label"],$rawField["name"]);
                }elseif(in_array($rawField["type"], ["array", "simple_array"])){
                    $field = new Field\ArrayField($rawField["label"],$rawField["name"]);
                }else{
                    switch($rawField["type"]){
                        case "string": $field = new Field\TextLine($rawField["label"],$rawField["name"]);break;
                        case "text": $field = new Field\TextArea($rawField["label"],$rawField["name"]);break;
                        case "boolean": $field = new Field\Checkbox($rawField["label"],$rawField["name"]);break;
                        case "PasswordField": 
                            $field = new Field\Password($rawField["label"],$rawField["name"], $options = [
                                "hashFunction" => $rawField["VPFAnnotation"]->hashFunction,
                            ]);
                        break;
                        case "TextLineField":
                            $field = new Field\TextLine($rawField["label"],$rawField["name"], $options = [
                                "pattern" => $rawField["VPFAnnotation"]->pattern,
                                "patternMessage" => $rawField["VPFAnnotation"]->patternMessage,
                                "minLength" => $rawField["VPFAnnotation"]->minLength,
                                "maxLength" => $rawField["VPFAnnotation"]->maxLength,
                            ]);
                        break;
                        case "FileField": 
                            $field = new Field\File($rawField["label"],$rawField["name"], $options = [
                                "extensions" => $rawField["VPFAnnotation"]->extensions,
                                "folder" => $rawField["VPFAnnotation"]->folder,
                            ]);
                        break;
                        case "RelationField": 
                            $field = new Field\Relation($rawField["label"],$rawField["name"], $options = [
                                "elements" => $rawField["VPFAnnotation"]->getElements(),
                                "repositoryClass" => $rawField["VPFAnnotation"]->repositoryClass,
                            ]);
                        break;
                        case "EnumField": 
                            $field = new Field\Select($rawField["label"],$rawField["name"], $options = [
                                "elements" => $rawField["VPFAnnotation"]->getElements()
                                ]);
                        break;
                    }
                }
                $field->setNullable($rawField["nullable"]);
                $fields[$field->getName()] = $field;
            }
        }
        return $fields;
    }

    /**
     * Retirer certains champs du formulaire
     * @param string ...$fieldsToRemove
     * 
     * @return Form
     */
    public function removeFields(string ...$fieldsToRemove):Form
    {
        foreach($fieldsToRemove as $field){
            if(in_array($field, array_keys($this->fields))){
                unset($this->fields[$field]);
            }
        }
        return $this;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function addField(Field\AbstractField $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * Retourne l'objet Field correspondant au nom en paramètre
     * @return Field
     */
    public function __get($name) 
    {
        return $this->fields[$name];
    }

    /**
     * Vérifier si le formulaire a été envoyé
     * @return bool
     */
    public function isSubmitted()
    {
        return isset($this->parameters[$this->name]);
    }

    /**
     * Vérifier si le formulaire a un message d'erreur
     * @return bool
     */
    public function hasError()
    {
        return $this->isSubmitted() && !$this->isValid();
    }

    public function createHTML()
    {
        $html = "
            <input type='hidden' name='".$this->name."'/>
        ";
        if ($this->error != '') {
            $html .= '<div class="form-error alert alert-warning">'.$this->error.'</div>';
        }
        foreach ($this->fields as $field) {
            $value = null;
            if (!$field->isIgnored() && !($field instanceof Field\Password) && $this->object != null)
                $value = ObjectReflection::getPropertyValue($this->object, $field->getName());
            $value = $this->parameters[$field->getName()] ?? $value;
            $html .= $field->getHTML($value);
        }
        return $html;
    }

    public function isValid()
    {
        $valid = true;
        foreach ($this->fields as $field) {
            if ($field instanceof Field\Password && $field->isDouble()) {
                if (!$field->isValid($this->parameters[$field->getName()], $this->parameters[$field->getConfirmName()])) {
                    $valid = false;
                }
            } elseif (!$field->isValid($this->parameters[$field->getName()] ?? null)) {
                $valid = false;
            }
        }
        return $valid;

    }

    /**
     * @param string $fieldName
     * @return Valeur du champ $fieldName
     */
    public function get(string $fieldName){
        $field = $this->fields[$fieldName];
        if ($field instanceof Field\File) {
            $value = $field->getFileBaseName();
            if($value == ""){
                $valueFromObject = ObjectReflection::getPropertyValue($this->object, $field->getName());
                if($valueFromObject != null){
                    $value = $valueFromObject;
                }
            }
        }else{
            $value = $field->getRealValue($this->parameters[$field->getName()] ?? null);
        }
        return $value;
    }

    public function updateObject()
    {
        if($this->object != null){
            foreach ($this->fields as $field) {
                if (!$field->isIgnored()) {
                    ObjectReflection::setPropertyValue($this->object, $field->getName(), $this->get($field->getName()));
                }
            }
        }else{
            throw new FormException("Aucun objet n'est géré par le formulaire (\$object = null)");
        }
    }
}
