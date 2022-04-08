<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Form;

/**
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 * 
 * This exception is thrown if no parameters are set to the form
 */
class NoParametersException extends \Exception 
{
    public function __construct(){
        parent::__construct__("Les parametres de la requête n'ont pas été passés au formulaire");
    }
}