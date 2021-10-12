<?php

namespace VPFramework\Form\Field;

use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\Form\Field\Field;

class Password extends Field
{

    public function getHashFunction(){ 
        if(isset($this->options["hashFunction"]))
            return $this->options["hashFunction"]; 
        else
            throw new \Exception("L'option hashFunction n'a pas été renseignée");
    }
    
    public function getFieldHTML(){
        //Nothing here
    }
    
    public function createHTML(){
        $html = '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                <input type="password" name="'.$this->name.'" class="form-control" id="'.$this->name.'">
                <span class="form-error">'.$this->error.'</span>
            </div>
        ';
        if(isset($this->options["isDouble"]) && $this->options["isDouble"]){
            $html .= '
                <div class="form-group">
                    <label class="form-label" for="confirm-'.$this->name.'">'.$this->options["secondLabel"].'</label>
                    <input type="password" name="confirm-'.$this->name.'" class="form-control" id="'.$this->name.'" >
                </div>
            ';
        }
        return $html;
    }

    public function getRealValue($value)
    { 
        return $this->getHashFunction()($value);
    }

    public function isValid($value)
    {
        if(parent::isValid($value)){
            if(isset($this->options["isDouble"]) && $this->options["isDouble"]){
                if($value != DIC::getInstance()->get(Request::class)->get("confirm-".$this->name)){
                    $this->error = "Les deux mots de passe ne sont pas identiques";
                    return false;
                }else{
                    return true;
                }
            }
            return true;
        }
        return false;
    }

}