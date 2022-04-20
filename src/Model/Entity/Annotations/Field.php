<?php
namespace VPFramework\Model\Entity\Annotations;

/**
 * Annotation permettant de définir des éléments supplémetaires pour une entité
 * Ex : Des types personnalisés qui seront utilisés par le framework comme le type FILE
 * 
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Field
{
    /**
     * Le texte à afficher si la propriété est utilisée dans un formulaire
     */
    public $label = "";
}
