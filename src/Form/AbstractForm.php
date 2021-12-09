<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Form;

use VPFramework\Form\Field\AbstractField;

/**
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 */
abstract class AbstractForm 
{
    /**
     * This name is assigned automatically in the constructor
     */
    protected $name;

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * Each field of the form has its render code in this variable
     * @var string[]
     */
    protected $htmls;

    protected $repository;
    protected $repositoryClass;
    protected $parameters = null;
    protected $validity = null;
    protected $error = "";

    /**
     * The inherited class must call this constructor at the end their own constructors
     * The reason : This constructor is going to build the form
     */
    public function __construct()
    {
        $getCalledClass = explode("\\", get_called_class());
        $this->name = "form-".end($getCalledClass);

        $this->build();
    }

    public function setParameters($parameters){
        $this->parameters = $parameters;
        return $this;
    }
    public function getFields()
    {
        return $this->fields;
    }

    public function addField(AbstractField $field)
    { 
        $this->fields[] = $field;
        return $this;
    }

    public function isSubmitted()
    {
        if($this->parameters !== null){
            if(isset($this->parameters[$this->name])){
                return true;
            }
            return false;
        }else{
            throw new NoParametersException();
        }
    }

    public function hasError()
    {
        if(!$this->isSubmitted())
            return false;
        return !$this->isValid();
    }

    public function __toString()
    {
        return $this->createHTML();
    }
    

    /**
     * In this method, the user(developer) is asked to add the fields of the form
     */
    abstract public function build();

    abstract public function createHTML();

    abstract public function isValid();

    abstract public function serialize();

}