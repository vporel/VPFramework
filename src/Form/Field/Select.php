<?php

namespace VPFramework\Form\Field;

class Select extends Field
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption("elements", null);
        parent::__construct($label, $name, $options);        
    }
    public function getElements(){
        if($this->options["elements"] != null){ 
            return $this->options["elements"];
        }else
            throw new \Exception("Les éléments pour le champ select n'ont pas été fournis");
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
                <select name="'.$this->name.'">
        ';
        foreach($this->getElements() as $value => $text){
            $select .= '<option value="'.$value.'" '.($value == $this->getDefault() ? 'selected' : '').'>'.$text.'</option>';
        }

        $select .= '</select>';
        return $select;
    }
}