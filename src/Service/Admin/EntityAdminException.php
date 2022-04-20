<?php
/*
 * This file is part of VPFramework Framework
 *
 * (c) Porel Nkouanang
 *
 */
namespace VPFramework\Service\Admin;

/**
 * @author Porel Nkouanang <dev.vporel@gmail.com>
 * 
 * Exception lancée si un problème survient dans l'analyse de l'entité devrant être gérée par le service d'administration (EntityAdmin)
 */
class EntityAdminException extends \Exception 
{
    public function __construct($msg){
        parent::__construct($msg);
    }
}