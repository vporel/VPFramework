<?php

namespace VPFramework\Form\Field;

/**
 * Représente un champ password dans le formulaire
 */
class Password extends AbstractInput
{
    /**
     * @var string
     */
    protected $hashFunction;
    /**
     * @var bool
     */
    protected $double = false;// Si ce champ doit créer un champ de confirmation
    /**
     * @var string
     */
    protected $secondLabel = "Confirmation";// Nom du champ de confirmation Si double = true
    
    public function __construct(string $label, string $name, $hashFunction = "sha1")
    {            
        parent::__construct($label, $name);    
        $this->hashFunction = $hashFunction;    
    }

    protected function getInputType(){ return "password";}

    public function getConfirmName(){ return "confirm-".$this->name;}
    
    protected function getCustomHTMLForFilter():string{}
    
    public function getHTML($value){
        $html = '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                <input type="password" name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.$this->getReadOnlyText().'>
                <span class="form-field-error text-error">'.$this->error.'</span>
            </div>
        ';
        if($this->double){
            $html .= '
                <div class="form-group">
                    <label class="form-label" for="'.$this->getConfirmName().'">'.$this->getSecondLabel().'</label>
                    <input type="password" name="'.$this->getConfirmName().'" class="form-control" id="'.$this->getConfirmName().'" '.$this->getReadOnlyText().'>
                </div>
            ';
        }
        return $html;
    }

    public function getRealValue($value)
    { 
        return $this->getHashFunction()($value);
    }

    public function isValid($value, $confirmValue = null):bool
    {
        if(parent::isValid($value)){
            if($this->double){
                if($confirmValue !== null){
                    if($value != $confirmValue){
                        $this->error = "Les deux mots de passe ne sont pas identiques";
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    throw new \Exception("La valeur du champ 'confirmation' n'a pas été passée en paramètre à la fonction isValid");
                }
            }else{
                return true;
            }
        }
        return false;
    }


    /**
     * Get the value of hashFunction
     *
     * @return  string
     */ 
    public function getHashFunction()
    {
        return $this->hashFunction;
    }

    /**
     * Set the value of hashFunction
     *
     * @param  string  $hashFunction
     *
     * @return  self
     */ 
    public function setHashFunction(string $hashFunction)
    {
        $this->hashFunction = $hashFunction;

        return $this;
    }

    /**
     * Get the value of $double
     *
     * @return  bool
     */ 
    public function isDouble()
    {
        return $this->double;
    }

    /**
     * Set the value of double
     *
     * @param  bool  $double
     *
     * @return  self
     */ 
    public function setDouble(bool $double)
    {
        $this->double = $double;

        return $this;
    }

    /**
     * Get the value of secondLabel
     *
     * @return  string
     */ 
    public function getSecondLabel()
    {
        return $this->secondLabel;
    }

    /**
     * Set the value of secondLabel
     *
     * @param  string  $secondLabel
     *
     * @return  self
     */ 
    public function setSecondLabel(string $secondLabel)
    {
        $this->secondLabel = $secondLabel;

        return $this;
    }
}