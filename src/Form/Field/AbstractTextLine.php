<?php

namespace VPFramework\Form\Field;

abstract class AbstractTextLine extends AbstractInput
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("minLength", 0);
        $this->addOption("maxLength", null);
        
        parent::__construct($label, $name, $options);      
    }

    protected function getCustomHTMLForFilter(): string
    {
        return "<input type='text'/>";
    }

    public function isValid($value){
        
        if(strlen($value) >= $this->getMinLength()){
            if($this->getMaxLength() === null || strlen($value) <= $this->getMaxLength()){
                return parent::isValid($value);
            }else{
                $this->error = "La longueur maximale est : ".$this->getMaxLength();
                return false;
            }
        }else{
            $this->error = "La longueur minimale est : ".$this->getMinLength();
            return false;
        }
        return true;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'minLength' => $this->getMinLength(),
            'maxLength' => $this->getMaxLength(),
            'pattern' => $this->getPattern()
        ]);
    }

}