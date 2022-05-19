<?php
namespace VPFramework\Model\Entity\Annotations;

/**
 * Annotation permettant de définir des éléments supplémetaires pour une entité
 * Ex : Un label qui sera utilisé dans les formulaires
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
