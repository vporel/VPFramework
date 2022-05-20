<?php
namespace VPFramework\Form\Annotations;

use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;

/**
 * This annotation should be used on entities properties
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

    public function getEntityClass(){
        return Repository::getRepositoryEntityClass($this->repositoryClass);
    }

    public function getElements()
    {
        if($this->elements === null)
            $this->elements = $this->getRepository()->findBy([], [Entity::getEntityNaturalOrderField($this->getEntityClass())]);
        return $this->elements;
    }

    public function getRepository()
    {
        return DIC::getInstance()->get($this->repositoryClass);
    }

    public function getKeyProperty(){
        return Entity::getEntityKeyProperty($this->getEntityClass());
    }

}
