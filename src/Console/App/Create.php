<?php

namespace VPFramework\Console\App;

use VPFramework\Console\Console;
use VPFramework\Console\App\Element\Controller;
use VPFramework\Console\App\Element\Entity;
use VPFramework\Console\App\Element\Repository;
use VPFramework\Console\App\Element\Form;

class Create extends \VPFramework\Console\Create
{

    public function getElements()
    {
        return [
            "controleur" => function(){Controller::create();},
            "formulaire" => function(){Form::create();},
            "Entite" => function(){Entity::create();},
            "repository" => function(){Repository::create();}
        ];
    }
}