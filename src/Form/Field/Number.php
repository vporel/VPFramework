<?php

namespace VPFramework\Form\Field;

class Number extends AbstractInput
{
    /**
     * @var int
     */
    protected $min = 0;
    /**
     * @var int|null
     */
    protected $max = null;
    
    public function __construct($label, $name)
    {
        parent::__construct($label, $name);
        $this->pattern = "#^([0-9]*\.?[0-9]+)?$#";
    }

    protected function getCustomHTMLForFilter(): string
    {
        return " >= <input type='number' data-type='min'/> ET <= <input type='number' data-type='max'/>";
    }

    protected function getInputType(){
        return "number";
    }

    public function isValid($value):bool
    {
        if((float) $value >= $this->getMin()){
            if($this->getMax() === null || (float) $value <= $this->getMax()){
                return parent::isValid($value);
            }else{
                $msg = "La valeur maximale est : ".$this->getMax();
            }
        }else{
            $msg = "La valeur minimale est : ".$this->getMin();
        }
    }


    /**
     * Get the value of max
     *
     * @return  int|null
     */ 
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set the value of max
     *
     * @param  int|null  $max
     *
     * @return  self
     */ 
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get the value of min
     *
     * @return  int
     */ 
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set the value of min
     *
     * @param  int  $min
     *
     * @return  self
     */ 
    public function setMin(int $min)
    {
        $this->min = $min;

        return $this;
    }
}