<?php
namespace VPFramework\Form\Annotations;

/**
 * This annotation should be used on entities properties
 * With this class, you can define extra property on a field which will be used in forms
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
