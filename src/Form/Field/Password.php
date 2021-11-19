<?php

namespace VPFramework\Form\Field;

class Password extends Input
{

    public function __construct($label, $name, $options = [])
    {
        $this
            ->addOption("hashFunction", null)
            ->addOption("isDouble", false)
            ->addOption("secondLabel", "Confirmation");
        parent::__construct($label, $name, $options);        
    }

    protected function getType(){ return "password";}

    public function getConfirmName(){ return "confirm-".$this->name;}

    public function getHashFunction(){ 
        if($this->options["hashFunction"] != null)
            return $this->options["hashFunction"]; 
        else
            throw new \Exception("L'option hashFunction n'a pas été renseignée");
    }
    
    public function createHTML(){
        $html = '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                <input type="password" name="'.$this->name.'" class="form-control" id="'.$this->name.'">
                <span class="form-field-error text-error">'.$this->error.'</span>
            </div>
        ';
        if($this->isDouble()){
            $html .= '
                <div class="form-group">
                    <label class="form-label" for="'.$this->getConfirmName().'">'.$this->getSecondLabel().'</label>
                    <input type="password" name="'.$this->getConfirmName().'" class="form-control" id="'.$this->getConfirmName().'" >
                </div>
            ';
        }
        return $html;
    }

    public function getRealValue($value)
    { 
        return $this->getHashFunction()($value);
    }

    public function isValid($value, $confirmValue = null)
    {
        if(parent::isValid($value)){
            if($this->isDouble() && $confirmValue !== null){
                if($value != $confirmValue){
                    $this->error = "Les deux mots de passe ne sont pas identiques";
                    return false;
                }else{
                    return true;
                }
            }else{
                throw new \Exception("La valeur du champ 'confirmation' n'a pas été passée en paramètre à la fonction isValid");
            }
        }
        return false;
    }

}