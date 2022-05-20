<?php

namespace VPFramework\InternalApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\InternalApp\App\Entity\AdminGroup;

class AdminGroupRepository extends Repository
{

    public function getEntityClass(){
        return AdminGroup::class;
    }
}
