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
use VPFramework\Form\Field\AbstractField;
use VPFramework\Form\Field\File;
use VPFramework\Form\Field\Password;
use VPFramework\Utils\ObjectReflection;
use Doctrine\ORM\EntityManager;
use VPFramework\Form\Field\Checkbox;
use VPFramework\Form\Field\Number;
use VPFramework\Form\Field\Relation;
use VPFramework\Form\Field\Select;
use VPFramework\Form\Field\TextArea;
use VPFramework\Form\Field\TextLine;
use VPFramework\Model\Entity\Entity;

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
            $field = null;
            switch($rawField["type"]){
                case "string": $field = new TextLine($rawField["label"],$rawField["name"]);break;
                case "text": $field = new TextArea($rawField["label"],$rawField["name"]);break;
                case "boolean": $field = new Checkbox($rawField["label"],$rawField["name"]);break;
                case "integer": $field = new Number($rawField["label"],$rawField["name"]);break;
                case "NumberField": $field = new Number($rawField["label"],$rawField["name"]);break;
                case "PasswordField": $field = new Password($rawField["label"],$rawField["name"]);break;
                case "FileField": $field = new File($rawField["label"],$rawField["name"]);break;
                case "RelationField": 
                    $field = new Relation($rawField["label"],$rawField["name"], $options = [
                        "elements" => $rawField["VPFAnnotation"]->getElements(),
                        "repositoryClass" => $rawField["VPFAnnotation"]->repositoryClass,
                    ]);
                case "EnumField": 
                    $field = new Select($rawField["label"],$rawField["name"], $options = [
                        "elements" => $rawField["VPFAnnotation"]->getElements()
                        ]);
                break;
            }
            $field->setNullable($rawField["nullable"]);
            $fields[$field->getName()] = $field;
        }
        return $fields;
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

    public function addField(AbstractField $field)
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
        
            $value = $this->parameters[$this->name] ?? null;
            if (!$field->isIgnored() && !($field instanceof Password || $field instanceof File))
                $value = ($this->object != null) ? ObjectReflection::getPropertyValue($this->object, $field->getName()) : null;
            
            $html .= $field->getHTML($value);
        }
        return $html;
    }

    public function isValid()
    {
        $valid = true;
        foreach ($this->fields as $field) {
            if (!($field instanceof File)) {
                if ($field instanceof Password) {
                    if ($field->isDouble() && !$field->isValid($this->parameters[$field->getName()], $this->parameters[$field->getConfirmName()])) {
                        $valid = false;
                        break;
                    }
                } elseif (!$field->isValid($this->parameters[$field->getName()])) {
                    $valid = false;
                }
            }
        }
        return $valid;

    }

    public function updateObject()
    {
        foreach ($this->fields as $field) {
            if (!$field->isIgnored()) {
                if ($field instanceof File) {
                    $value = $field->getFileBaseName();
                }else{
                    $value = $field->getRealValue($this->parameters[$field->getName()]);
                }
                ObjectReflection::setPropertyValue($this->object, $field->getName(), $value);
            } else {
                
            }
        }
    }
}
