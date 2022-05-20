<?php
namespace VPFramework\Form\Annotations;

use VPFramework\Core\DIC;

/**
 * This annotation should be used on entities properties
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EnumField extends Field
{
    private $elements;

    /**
     * Une classe implémentant l'interface VPFramework\Model\Entity\Enum
     */
    public $class;

    /**
     * Les éléments ne sont pas passés comme propriété de la classe car ils pourraient être 
     * récupérés dynamiquement
     */
    public function getElements()
    {
        if($this->elements === null)
            $this->elements = DIC::getInstance()->get($this->class)->list();
        return $this->elements;
    }

}
