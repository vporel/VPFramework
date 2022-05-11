<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class TextArea extends AbstractField
{
    
    protected function getCustomHTMLForFilter(): string{}
    public function getCustomHTML($value){
        $value = $value ?? $this->getDefault();
        return '<textarea name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.$this->getReadOnlyText().' '.(!$this->isNullable() ? 'required': '').'>'.$value.'</textarea>';
    }

}