<?php

namespace VPFramework\Form\Field;

abstract class AbstractInput extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("pattern", "^(.[\s]*.*)*$");
        parent::__construct($label, $name, $options);      
    }

    public function getFieldHTML(){
        return '<input  value="'.$this->getDefault().'" type="'.$this->getType().'" name="'.$this->name.'" class="form-control" id="'.$this->name.'" pattern="'.$this->getPattern().'" '.($this->isRequired() ? 'required': '').'>';
    }

    protected abstract function getType();

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'pattern' => $this->getPattern()
        ]);
    }

}