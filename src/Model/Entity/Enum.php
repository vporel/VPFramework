<?php
namespace VPFramework\Model\Entity;

/**
 * @author Porel NKOUANANG (delv.vporel@gmail.com)
 * 
 * 
 */
interface Enum
{
    /**
     * Liste des valeurs de l'énumération
     * @return array
     */
    public function list() :array;
}
