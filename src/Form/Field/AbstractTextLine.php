<?php

namespace VPFramework\Form\Field;

abstract class AbstractTextLine extends AbstractInput
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("minLength", 0);
        $this->addOption("maxLength", null);
        
        parent::__construct($label, $name, $options);   
        $this
            ->addValidationRule("La longueur minimale est : ".$this->getMinLength(), function($value){
                return strlen($value) >= $this->getMinLength();
            })
            ->addValidationRule("La longueur maximale est : ".$this->getMaxLength(), function($value){
                return $this->getMaxLength() === null || strlen($value) <= $this->getMaxLength();
            });   
    }

    protected function getCustomHTMLForFilter(): string
    {
        return "<input type='text'/>";
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