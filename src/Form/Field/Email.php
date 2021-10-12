<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class Email extends Field
{
    
    public function getFieldHTML(){
        return '<input type="email" name="'.$this->name.'" class="form-control" id="'.$this->name.'" value="'.$this->getDefault().'"/>';
    }

}