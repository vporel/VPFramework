<?php

namespace VPFramework\Doctrine;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use VPFramework\Core\DIC;

abstract class Repository extends EntityRepository
{
    public function __construct()
    {
        $calledClass = explode("\\",get_called_class());
        $entityClass = str_replace("Repository", "", end($calledClass));
        parent::__construct(DIC::getInstance()->get(EntityManager::class), new ClassMetadata("App\\Entity\\".$entityClass));
        
    }

}