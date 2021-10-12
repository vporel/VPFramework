<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class Number extends Field
{
    
    public function getFieldHTML(){
        return '<input type="number" name="'.$this->name.'" class="form-control" id="'.$this->name.'" value="'.$this->getDefault().'">';
    }

}