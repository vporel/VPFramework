<?php

namespace VPFramework\Form\Field;

class ValidationRule
{
    private $rule;
    private $message;

    /**
     * @param callable $rule Une fonction retournant un booleen
     * @param string $message Message si la règle n'est pas respectée
     */
    public function __construct(string $message, callable $rule)
    {
        $this->rule = $rule;
        $this->message = $message;
    }

    public function getRule(){
        return $this->rule;
    }

    public function getMessage(){
        return $this->message;
    }
  
}
