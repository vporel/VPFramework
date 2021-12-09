<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Form;

use VPFramework\Form\Field\File;
use VPFramework\Form\Field\Password;


/**
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 */
abstract class AbstractFormUnique extends AbstractForm
{
    /**
     * The object managed by the form
     */
    protected $object = null;

    public function __construct($object)
    {
        $this->object = $object;
        
        parent::__construct();
    }
    public function createHTML()
    {
        if($this->isSubmitted())
            $this->isValid();
        $html = '
            <input type="hidden" name="'.$this->name.'"/>
        ';
        if($this->error != "")
            $html .= '<div class="form-error alert alert-warning">'.$this->error.'</div>';
        if(isset($this->parameters[$this->name])){ // Condition de la fonction isSubmitted : Condition réécrite pour éviter que la function updateObject soit appelée
            
            foreach($this->fields as $field){
                if(!($field instanceof File))
                    $field->setDefault($this->parameters[$field->getName()]);
                $this->htmls[$field->getName()] = $field->createHTML();
                $html .= $this->htmls[$field->getName()];
            }
        }elseif($this->object != null && $this->object->getId() != null){
            foreach($this->fields as $field){
                if(!$field->isIgnored() && !($field instanceof Password) && !($field instanceof File)){
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
        if($this->validity === null){
            $valid = true;
            foreach($this->fields as $field)
            {
                if(!($field instanceof File)){
                    if($field instanceof Password){
                        if($field->isDouble() && !$field->isValid($this->parameters[$field->getName()], $this->parameters[$field->getConfirmName()]))
                            $valid = false;
                    }elseif(!$field->isValid($this->parameters[$field->getName()])){
                        $valid = false;
                    }
                }
            }
            if($valid)
                $this->updateObject();
            $this->validity = $valid;
        }
        return $this->validity;

    }

    public function serialize(){
        
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
                if($field instanceof File && $this->object->getId() != null){
                    $field->uploadFile($this->object->getId());
                }
            }
        }
    }

}