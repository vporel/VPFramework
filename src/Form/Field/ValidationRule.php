<?php

namespace VPFramework\Form\Field;

class ValidationRule
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var callable
     */
    private $rule;
    /**
     * @var string
     */
    private $message;

    /**
     * @param callable $rule Une fonction retournant un booleen
     * @param string $message Message si la règle n'est pas respectée
     */
    public function __construct($name, string $message, callable $rule)
    {
        $this->name = $name;
        $this->rule = $rule;
        $this->message = $message;
    }

    public function getName(){
        return $this->name;
    }
    public function getRule(){
        return $this->rule;
    }

    public function getMessage(){
        return $this->message;
    }
  
}
