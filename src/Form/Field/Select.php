<?php

namespace VPFramework\Form\Field;

class Select extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
        $this->addOption('elements', null);
        parent::__construct($label, $name, $options);
    }

    public function getElements()
    {
        if ($this->options['elements'] != null) {
            return $this->options['elements'];
        } else {
            throw new \Exception("Les éléments pour le champ select n'ont pas été fournis");
        }
    }

    protected function getCustomHTML($value)
    {
        $value = $value ?? $this->getDefault();
        if($this->isReadOnly()){
            $textToShow = "";
            foreach ($this->getElements() as $key => $text) {
                if($key == $value){
                    $textToShow = $text;
                    break;
                }
            }
            return "<span class='form-read-only-value'>$textToShow</span>";
        }else{
            $select = '
                    <select name="'.$this->name.'"  id="'.$this->name.'">
            ';
            foreach ($this->getElements() as $key => $text) {
                $select .= '<option value="'.$key.'" '.($key == $value ? 'selected' : '').'>'.$text.'</option>';
            }
            $select .= '</select>';

            return $select;
        }
    }

    protected function getCustomHTMLForFilter():string
    {
        $html = "<select>";
        $html .= "<option value=''>Peu importe</option>";
        foreach($this->getElements() as $option){
            $html .=  "<option value='$option'>$option</option>";
        }
        $html .= "</select>";
        return $html;
    }

    public function serialize()
    {
        $elementsValuesTexts = [];

        foreach ($this->getElements() as $value => $text) {
            $elementsValuesTexts[] = [
                'value' => $value,
                'text' => $text,
            ];
        }
        return array_merge(parent::serialize(), [
            'elements' => $elementsValuesTexts,
        ]);
    }
}
