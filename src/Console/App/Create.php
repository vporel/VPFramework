<?php

namespace VPFramework\Console\App;

use VPFramework\Console\Console;
use VPFramework\Console\App\Element\Controller;
use VPFramework\Console\App\Element\Entity;
use VPFramework\Console\App\Element\Form;

class Create extends \VPFramework\Console\Create
{

    public function getElements()
    {
        return [
            "controller" => function(){Controller::create();},
            "form" => function(){Form::create();},
            "entity" => function(){Entity::create();}
        ];
    }
}