<?php

namespace VPFramework\Form\Field;

class Checkbox extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
       
        parent::__construct($label, $name, $options);        
    }

    public function getRealValue($value)
    {
        if($value != null){
            return true;
        }else{
            return false;
        }
    }

    public function getCustomHTMLForFilter():string
    {
        return "
            <select>
                <option value=''>Peu importe</option>
                <option value='1'>Vrai</option>
                <option value='0'>Faux</option> 
            </select>
        ";
    }

    protected function getCustomHTML($value){
        if($this->isReadOnly()){
            if($this->getRealValue($value)){
                return "<span class='form-read-only-value'>Vrai</span>";
            }else{
                return "<span class='form-read-only-value'>Faux</span>";
            }
        }else{
            return '<input type="checkbox" name="'.$this->name.'" class="form-check-input" id="'.$this->name.'" '.(($value == 1 || $value == true) ? "checked" : "").' />';
        }
    }

    public function isValid($value)
    {
        return true;
    }

}