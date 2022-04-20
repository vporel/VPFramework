<?php

namespace VPFramework\DefaultApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\DefaultApp\App\Entity\AdminGroupPermission;

class AdminGroupPermissionRepository extends Repository
{

    public function getEntityClass(){
        return AdminGroupPermission::class;
    }
}
