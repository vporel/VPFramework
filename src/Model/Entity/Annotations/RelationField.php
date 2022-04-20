<?php
namespace VPFramework\Model\Entity\Annotations;

use VPFramework\Core\DIC;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RelationField extends Field
{

    /**
     * Le repository gérant l'entité
     * Ex : App\Repository\UserRepository
     */
    public $repositoryClass;

    public function getElements()
    {
        $repo = $this->getRepository();
        return $repo->findAll();
    }

    public function getRepository()
    {
        return DIC::getInstance()->get($this->repositoryClass);
    }

}
