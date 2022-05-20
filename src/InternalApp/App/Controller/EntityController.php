<?php
namespace VPFramework\InternalApp\App\Controller;

use VPFramework\Core\Controller;
use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\Model\Entity\Entity;
use VPFramework\Model\Repository\Repository;
use VPFramework\Utils\ObjectReflection;

class EntityController extends Controller
{

	public function jsonList(Request $request){
        $repositoryClass = $request->get("repositoryClass");
        $entityClass = Repository::getRepositoryEntityClass($repositoryClass);
		$keyProperty = Entity::getEntityKeyProperty($entityClass);
		$orderField = Entity::getEntityNaturalOrderField($entityClass);
		$elements = DIC::getInstance()->get($repositoryClass)->findBy([], [$orderField]);
		
		$jsonElements = [];
		foreach($elements as $element){
			$jsonElements[] = [
				"value" => ObjectReflection::getPropertyValue($element, $keyProperty),
				"text" => (string) $element,
			];
		}
		return json_encode($jsonElements);
	}
}