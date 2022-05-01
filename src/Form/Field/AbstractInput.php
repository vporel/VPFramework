<?php

namespace VPFramework\Form\Field;

abstract class AbstractInput extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("pattern", "#^(.[\s]*.*)*$#");
        $this->addOption("patternMessage", "Ne respecte pas le motif défini");
        
        parent::__construct($label, $name, $options);      
    }

    /**
     * Retourne la valeur de l'attribut type dans la balise <input>
     * Ex : email
     */
    protected abstract function getInputType();

    public function getCustomHTML($value){
        $value = $value ?? $this->getDefault();
        return '<input  value="'.$value.'" type="'.$this->getInputType().'" name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.(!$this->isNullable() ? 'required': '').'>';
    }

    public function isValid($value){
        if(parent::isValid($value)){
           if(!preg_match($this->getPattern(), $value)){
                $this->error = $this->getPatternMessage();
                return false;
            }
        }
        return true;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'min' => $this->getMin(),
            'max' => $this->getMax(),
            'pattern' => $this->getPattern()
        ]);
    }

}