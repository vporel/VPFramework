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
            return $value;
        }else{
            false;
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
        return '<input type="checkbox" name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.(($value == 1 || $value == true) ? "checked" : "").'/>';
    }

}