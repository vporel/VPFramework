<?php

namespace VPFramework\DefaultApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\DefaultApp\App\Entity\Admin;
use Doctrine\ORM\Mapping\ClassMetadata;

class AdminRepository extends Repository
{

    public function getEntityClass(){
        return Admin::class;
    }
}
