<?php

namespace VPFramework\Form\Field;

abstract class AbstractInput extends AbstractField
{
    /**
     * @var string
     * Expression régulière que doit respecter la valeur
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $patternMessage;

    public function __construct($label, $name, $options = [])
    {
        parent::__construct($label, $name, $options);  
        $this->setPattern("#^(.[\s]*.*)*$#", "Ne respecte pas le motif défini");   
    }

    /**
     * Retourne la valeur de l'attribut type dans la balise <input>
     * Ex : email
     */
    protected abstract function getInputType();

    public function getCustomHTML($value){
        $value = $value ?? $this->defaultValue;
        return '<input  value="'.$value.'" type="'.$this->getInputType().'" name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.(!$this->isNullable() ? 'required': '').' '.$this->getReadOnlyText().'>';
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'pattern' => $this->pattern,
            'patternMessage' => $this->patternMessage
        ]);
    }


    /**
     * Get expression régulière que doit respecter la valeur
     *
     * @return  string
     */ 
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set expression régulière que doit respecter la valeur
     *
     * @param  string  $pattern  Expression régulière que doit respecter la valeur
     * @param  string  $patternMessage  EMEssage en cas d'erreur
     *
     * @return  self
     */ 
    public function setPattern(string $pattern, string $patternMessage)
    {
        $this->pattern = $pattern;
        $this->patternMessage = $patternMessage;
        $this->addValidationRule("pattern", $this->getPatternMessage(), function($value){
            return preg_match($this->getPattern(), $value);
        }); 

        return $this;
    }

    /**
     * Get the value of patternMessage
     *
     * @return  string
     */ 
    public function getPatternMessage()
    {
        return $this->patternMessage;
    }
}