<?php

namespace VPFramework\Console;

abstract class Create extends Command
{
    public function execute()
    {
        $elements = $this->getElements();
        if(count($this->parameters) < 1){
            $elementToCreate = Console::input("Que voulez-vous créer ? (".implode(", ", array_keys($elements)).") ? : ");
            
        } else{
            $elementToCreate = $this->parameters[0]; 
        }
        if(array_key_exists($elementToCreate, $elements)){
            $elements[$elementToCreate]();
        }else{
            echo "\nL'élément ('$elementToCreate') n'est pas reconnu\n";
            echo "\nLes éléments reconnus sont : ".implode(", ", array_keys($elements));
        }
        
    }
    
    /**
     * @return array
     */
    public abstract function getElements();
}