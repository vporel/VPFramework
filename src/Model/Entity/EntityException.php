<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Model\Entity;

/**
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 * 
 * 
 *
 */
class EntityException extends \Exception 
{
    public function __construct($msg){
        parent::__construct($msg);
    }
}