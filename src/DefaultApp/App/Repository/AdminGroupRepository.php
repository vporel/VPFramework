<?php

namespace VPFramework\DefaultApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\DefaultApp\App\Entity\AdminGroup;

class AdminGroupRepository extends Repository
{

    public function getEntityClass(){
        return AdminGroup::class;
    }
}
