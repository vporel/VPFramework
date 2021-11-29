<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class TextArea extends AbstractField
{
    
    public function getFieldHTML(){
        return '<textarea name="'.$this->name.'" class="form-control" id="'.$this->name.'">'.$this->getDefault().'</textarea>';
    }

}