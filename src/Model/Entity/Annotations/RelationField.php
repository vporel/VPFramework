<?php
namespace VPFramework\Model\Entity\Annotations;

use VPFramework\Core\DIC;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RelationField extends Field
{
    private $elements;
    /**
     * Le repository gérant l'entité
     * Ex : App\Repository\UserRepository
     */
    public $repositoryClass;

    /**
     * Le champ de l"entité dont la valeur sera conservée
     * @default "id"
     */
    public $keyField = "id";

    public function getElements()
    {
        if($this->elements === null)
            $this->elements = $this->getRepository()->findAll();
        return $this->elements;
    }

    public function getRepository()
    {
        return DIC::getInstance()->get($this->repositoryClass);
    }

}
