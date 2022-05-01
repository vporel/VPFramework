<?php

namespace VPFramework\Form\Field;

class Number extends AbstractInput
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("min", 0);
        $this->addOption("max", null);
        parent::__construct($label, $name, $options);
        $this->setPattern("#^[0-9]+$#");
    }

    protected function getCustomHTMLForFilter(): string
    {
        return " >= <input type='number' data-type='min'/> ET <= <input type='number' data-type='max'/>";
    }

    protected function getInputType(){
        return "number";
    }

    public function isValid($value){
        if((float) $value >= $this->getMin()){
            if($this->getMax() === null || (float) $value <= $this->getMax()){
                return parent::isValid($value);
            }else{
                $msg = "La valeur maximale est : ".$this->getMax();
            }
        }else{
            $msg = "La valeur minimale est : ".$this->getMin();
        }
    }

}