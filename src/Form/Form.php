<?php

namespace VPFramework\Form;

use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\Form\Field\Relation;
use VPFramework\Form\Field\Field;

abstract class Form 
{
    protected $name;

    /**
     * @var Field[]
     */
    protected $fields;

    protected $htmls;

    protected $object = null;
    protected $repository;
    protected $repositoryClass;
    protected $parameters = null;
    protected $checked = false;
    protected $error = "";

    public function __construct($object, $repositoryClass)
    {
        $getCalledClass = explode("\\", get_called_class());
        $this->name = end($getCalledClass);
        $this->object = $object;
        $this->repositoryClass = $repositoryClass;
        $this->repository = DIC::getInstance()->get($repositoryClass);

        $this->build();
    }

    public function setParameters($parameters){
        $this->parameters = $parameters;
        return $this;
    }
    public function getFields()
    {
        return $this->fields;
    }

    public abstract function build();

    public function createHTML()
    {
        $html = '
            <input type="hidden" name="form-'.$this->name.'"/>
        ';
        if($this->error != "")
            $html .= '<div class="form-error alert alert-warning">'.$this->error.'</div>';
        if(isset($this->parameters["form-".$this->name])){ // Condition de la fonction isSubmitted : Condition réécrite pour éviter que la function updateObject soit appelée
            if(!$this->checked)
                $this->isValid();
            foreach($this->fields as $field){
                if($field->getClass() != "File")
                    $field->setDefault($this->parameters[$field->getName()]);
                $this->htmls[$field->getName()] = $field->createHTML();
                $html .= $this->htmls[$field->getName()];
            }
        }elseif($this->object->getId() != null){
            foreach($this->fields as $field){
                if(!$field->isIgnored() && $field->getClass() != "Password" && $field->getClass() != "File"){
                    $method = "get".ucfirst($field->getName());
                    $field->setDefault($this->object->$method());
                }
                $this->htmls[$field->getName()] = $field->createHTML();
                $html .= $this->htmls[$field->getName()];
            }
        }else{
            foreach($this->fields as $field){
                $this->htmls[$field->getName()] = $field->createHTML();
                $html .= $this->htmls[$field->getName()];
            }
        }
        
        return $html;

    }

    public function isValid(){
        $this->checked = true;
        foreach($this->fields as $field)
        {
            if($field->getClass() != "File" && !$field->isValid($this->parameters[$field->getName()]))
                return false;
        }
        $this->updateObject();
        return true;

    }

    public function serialize($parameters){
        $data = [];
        foreach($this->groups as $group){
            $fields = $group->getFields();
            for($i = 0;$i<count($fields);$i++){
                $field = $fields[$i];
                if($field->getType() != "FormConfirmPassword"){
                    if($field->getType() == "FormPassword"){
                        $hash = $field->getHashFunction();
                        $data[$field->getName()] = $hash($parameters[$field->getName()]);
                    }else
                        $data[$field->getName()] = $parameters[$field->getName()];
                }
            }
        }
        return $data;
    }

    public function addField(Field $field)
    { 
        $this->fields[] = $field;
        return $this;
    }

    public function isSubmitted()
    {
        if($this->parameters !== null){
            if(isset($this->parameters["form-".$this->name])){
                return true;
            }
            return false;
        }else{
            throw new \Exception("Les parametres de la requête n'ont pas été passés au formulaire");
        }
    }

    public function updateObject()
    {
        foreach($this->fields as $field){
            if(!$field->isIgnored()){
                $method = "set".ucfirst($field->getName());
                $value = $field->getRealValue($this->parameters[$field->getName()]);
                if(method_exists($this->object, $method)){
                    $this->object->$method($value);
                }else{
                    throw new \Exception("La méthode $method n'existe pas : classe ".get_class($this->object));
                }
            }else{
                if($field->getClass() == "File" && $this->object->getId() != null){
                    $field->uploadFile($this->object->getId());
                }
            }
        }
    }

    public function hasError()
    {
        if(!$this->isSubmitted())
            return false;
        if(!$this->checked)
            $this->isValid();
        return $this->error != "";
    }

    public function __toString()
    {
        return $this->createHTML();
    }

}