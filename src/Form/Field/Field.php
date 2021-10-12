<?php

namespace VPFramework\Form\Field;

abstract class Field
{
    protected
        $label,
        $name,
        $options,
        $error = "";

    /**
     * Constructeur
     * @param $label
     * @param $name
     * @param $options
     */
    public function __construct($label, $name, $options = [])
    {
        $this->label = $label;
        $this->name = $name;
        $this->options = $options;
        
    }

    public function getClass(){
        $classNameExplode = explode("\\", get_called_class());
        return end($classNameExplode);
    }

   

    public function getLabel(){ return $this->label; }

    public function getName(){ return $this->name; }

    public function getDefault(){ return isset($this->options["default"]) ? $this->options["default"] : ""; }

    public function setDefault($default){ 
        $this->options["default"] = $default;  
        return $this;
    }

    public function getPattern(){
        if(isset($this->options["pattern"])){
            return $this->options["pattern"];
        }else{
            if($this->getClass() == "Number"){
                return "^[0-9]+$";
            }else{
                return "^(.[\s]*.*)*$";
            }
        } 
    }

    public function setPattern($pattern){
       $this->options["pattern"] = $pattern;
       return $this;
    }

    public function getError(){ return $this->error; }

    public function isRequired(){ return (isset($this->options["required"]) && $this->options["required"]); }

    public function isIgnored(){ return (isset($this->options["isIgnored"]) && $this->options["isIgnored"]); }

    public function getRealValue($value){
        return $value;
    }

    public abstract function getFieldHTML();

    public function createHTML(){
        /*
            Cette function est faite pour être redéfinie par la class Password uniquement
         */
        return '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                '.$this->getFieldHTML().'
                <span class="form-field-error text-bad">'.$this->error.'</span>
            </div>
        ';
    }
    
    public function isValid($value){
        if($this->isRequired())
            if(trim($value) != ""){
                return true;
            }else{
                $this->error = "Ce champ doit être renseigné";
                return false;
            }
        else
            return true;
    }

}