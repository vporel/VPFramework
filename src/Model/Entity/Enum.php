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
     * Les clés sont les valeurs gardées et les valeurs associées aux clés sont affichées dans les formulaires
     * @return array<int|string, string>
     */
    public function list() :array;
}
