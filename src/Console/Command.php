<?php

namespace VPFramework\Console;

abstract class Command
{
    protected $parameters;
    
    public function __construct(array $parameters){
        $this->parameters = $parameters;
    }

    abstract public function execute();

}