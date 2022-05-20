<?php

namespace VPFramework\Form\Field;

abstract class AbstractTextLine extends AbstractInput
{
    /**
     * @var int
     */
    protected $minLength = 0;

    /**
     * @var int|null
     */
    protected $maxLength = null;
    public function __construct(string $label, string $name)
    {
        
        parent::__construct($label, $name);   
        $this
            ->addValidationRule("La longueur minimale est : ".$this->getMinLength(), function($value){
                return strlen($value) >= $this->getMinLength();
            })
            ->addValidationRule("La longueur maximale est : ".$this->getMaxLength(), function($value){
                return $this->getMaxLength() === null || strlen($value) <= $this->getMaxLength();
            });   
    }

    protected function getCustomHTMLForFilter(): string
    {
        return "<input type='text'/>";
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ]);
    }


    /**
     * Get the value of minLength
     *
     * @return  int
     */ 
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Set the value of minLength
     *
     * @param  int  $minLength
     *
     * @return  self
     */ 
    public function setMinLength(int $minLength)
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * Get the value of maxLength
     *
     * @return  int|null
     */ 
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set the value of maxLength
     *
     * @param  int|null  $maxLength
     *
     * @return  self
     */ 
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }
}