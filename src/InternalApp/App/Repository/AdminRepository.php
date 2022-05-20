<?php

namespace VPFramework\InternalApp\App\Repository;

use VPFramework\Model\Repository\Repository;
use VPFramework\InternalApp\App\Entity\Admin;

class AdminRepository extends Repository
{

    public function getEntityClass(){
        return Admin::class;
    }
}
