<?php

namespace VPFramework\Form\Field;

class Select extends AbstractField
{
    /**
     * @var array
     */
    private $elements = null;

    public function __construct(string $label, string $name, array $elements)
    {
        parent::__construct($label, $name);
        $this->elements = $elements;
    }

    public function getElements()
    {
        return $this->elements;
    }

    protected function getCustomHTML($value)
    {
        $value = $value ?? $this->getDefaultValue();
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
                    <select name="'.$this->name.'"  id="'.$this->name.'" class="form-select">
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
