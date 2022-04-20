<?php
namespace VPFramework\Model\Entity\Annotations;

use VPFramework\Core\DIC;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EnumField extends Field
{

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
        $classObject = DIC::getInstance()->get($this->class);
        return $classObject->list();
    }

}
