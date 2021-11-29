<?php

namespace VPFramework\Form\Field;

class Number extends AbstractInput
{
    public function __construct($label, $name, $options = [])
    {
        parent::__construct($label, $name, $options);
        $this->setPattern("^[0-9]+$");
    }

    protected function getType(){
        return "number";
    }

}