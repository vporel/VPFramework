<?php

namespace VPFramework\Console;

abstract class Create extends Command
{
    public function execute()
    {
        $elements = $this->getElements();
        if(count($this->parameters) < 1){
            $elementToCreate = Console::input("What do you want to create (".implode(", ", array_keys($elements)).") ? : ");
            
        } else{
            $elementToCreate = $this->parameters[0]; 
        }
        if(array_key_exists($elementToCreate, $elements)){
            $elements[$elementToCreate]();
        }else{
            echo "\nThis element ('$elementToCreate') is not known\n";
        }
    }
    
    /**
     * @return array
     */
    public abstract function getElements();
}