<?php

namespace VPFramework\InternalApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\InternalApp\App\Entity\AdminGroupPermission;

class AdminGroupPermissionRepository extends Repository
{

    public function getEntityClass(){
        return AdminGroupPermission::class;
    }
}
