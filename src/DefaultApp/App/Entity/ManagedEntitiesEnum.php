<?php

namespace VPFramework\DefaultApp\App\Entity;

use VPFramework\Core\DIC;
use VPFramework\Model\Entity\Enum;

class ManagedEntitiesEnum implements Enum
{
    public function list():array{
        $list = [];
        $serviceConfig = DIC::getInstance()->get("VPFramework\Core\Configuration\ServiceConfiguration");
        $this->entitiesAdmin = $serviceConfig->getService("admin");
		foreach($this->entitiesAdmin as $entityAdmin){
            if(!$entityAdmin->isBuiltin())
			$list[] = $entityAdmin->getEntityClass();
		}
        return $list;
    }
}
