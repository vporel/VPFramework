<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class Select extends Field
{

    public function getElements(){
        if(isset($this->options["elements"])){
            return $this->options["elements"];
        }else
            throw new \Exception("Les élément spour le champ select n'ont pas été fournis");
    }

    public function getElementsJSON()
    {
        $array = [];
       
        foreach($this->getElements() as $value => $text){
            $array[] = [
                "value" => $value,
                "text" => $text,
            ];
        }
        return json_encode(["elements" => $array]);
    }

    public function getFieldHTML(){
        $select = '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                <select name="'.$this->name.'">
        ';
        foreach($this->getElements() as $value => $text){
            $select .= '<option value="'.$value.'" '.($value == $this->getDefault() ? 'selected' : '').'>'.$text.'</option>';
        }

        $select .= '</select>';
        return $select;
    }
}