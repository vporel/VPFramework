<?php

namespace VPFramework\Form\Field;

class ArrayField extends AbstractField
{

    public function getCustomHTMLForFilter():string{}

    public function getRealValue($values)
    {
        if($values == null){
            return $values;
        }else{
            if(is_array($values)){
                $newArray  = [];
                foreach($values as $value){
                    if(trim($value) !="")
                        $newArray[] = $value;
                }
                return $newArray;
            }else{
                throw new \Exception("ArrayField : La valeur passée n'est pas un tableau");
            }
        }
    }

    protected function getCustomHTML($values){
        if($values == null || is_array($values)){
            if($this->readOnly){
                if($values != null)
                    return "<span class='form-read-only-value'>".implode(" | ", $values)."</span>";
                else
                    return '';
            }else{
                $html = '';
                if($values != null){
                    foreach($values as $value){
                        $html .= '<input type="text" name="'.$this->name.'[]" class="form-control" value="'.$value.'" /><br>';
                    }
                }else{                        
                    $html .= '<input type="text" name="'.$this->name.'[]" class="form-control" /><br>';
                }
                $html .= '<button type="button" class="add-array-element-button" id="'.$this->name.'-add" onclick="add_'.$this->name.'_element()">+</button>';
                $html .= '
                    <script type="text/javascript">
                        function add_'.$this->name.'_element(){
                            let button = document.getElementById("'.$this->name.'-add");
                            button.insertAdjacentHTML(\'beforebegin\', \'<input type="text" name="'.$this->name.'[]" class="form-control" /><br>\');
                        }
                    </script>
                ';
                return $html;
            }
        }else{
            throw new \Exception("ArrayField : La valeur passée n'est pas un tableau");
        }
    }

}