<?php
namespace VPFramework\Model\Entity;

/**
 * Interface implémenté par des entité associées à des images
 */
interface HasImage{

    /**
     * Retourne le dossier contenant les images, La racine est le dossier "Public"
     */
    public function getImageFolder();
    
}