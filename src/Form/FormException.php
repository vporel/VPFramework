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
 * 
 *
 */
class FormException extends \Exception 
{
    public function __construct($msg){
        parent::__construct($msg);
    }
}