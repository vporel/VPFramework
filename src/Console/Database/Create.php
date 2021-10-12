<?php

namespace VPFramework\Console\Database;

use VPFramework\Console\Command;
use VPFramework\Console\Console;
use VPFramework\Console\Database\Element\Entity;


class Create extends Command
{

    public function execute()
    {
        if(count($this->parameters) < 1){
            $elementToCreate = Console::input("What do you want to create ?");
            
        } else{
            $elementToCreate = $this->parameters[0];
        }
        $method = "create".ucfirst($elementToCreate);
        if(method_exists($this, $method)){
            $this->$method();
        }else{
            echo "\nThis element is not known\n";
        }
    }

    public function createDatabase()
    {

    }

    public function createEntity()
    {   
        // Creation of files
        $entity = Entity::create();
    }
}