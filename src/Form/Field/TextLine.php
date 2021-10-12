<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class TextLine extends Field
{
    
    public function getFieldHTML(){
        return '<input type="text" name="'.$this->name.'" class="form-control" id="'.$this->name.'" value="'.$this->getDefault().'">';
    }

}