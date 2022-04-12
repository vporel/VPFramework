<?php
namespace VPFramework\DefaultApp\App\Controller;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\DefaultApp\App\Repository\AdminRepository;

class EntityAdminController extends DefaultAppController
{
	private $em, $entitiesAdmin, $entityAdmin, $request;
	public function __construct(EntityManager $em, ServiceConfiguration $serviceConfig, Request $request){
		$this->em = $em;
		$this->request = $request;
		$this->entitiesAdmin = $serviceConfig->getService("admin");
		foreach($this->entitiesAdmin as $entityAdmin){
			if($entityAdmin->getName() == $this->request->get("entityName"))
				$this->entityAdmin = $entityAdmin;
		}
	}

	public function list(){
		$fields = [];
		if(count($this->entityAdmin->getMainFields()) > 0){
			foreach(array_keys($this->entityAdmin->getFields($this->em)) as $field){
				if(in_array($field, $this->entityAdmin->getMainFields()))
					$fields[] = $field;
			}
		}else{
			$fields = array_keys($this->entityAdmin->getFields($this->em));
		}
		$elements = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findBy([], ["-id"]);
		return $this->render("admin/entity/list.php", [
			"entityAdmin" => $this->entityAdmin->getName(), 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"fields" => $fields,
			"elements" => $elements
		]);
	}

	public function add(){
		
		$fields = $this->entityAdmin->getFields($this->em);
		$class = $this->entityAdmin->getEntityClass();
		$element = new $class();
		$msg = "";
		if($this->request->getMethod() == "POST"){
			$metaData = $this->entityAdmin->getMetaData($this->em);
			$allFieldsFilled = true;
			foreach($fields as $fieldName => $type){
				if($this->request->get($fieldName) !== null){
					if(!$metaData->isNullable($fieldName) && trim($this->request->get($fieldName)) == ""){
						$allFieldsFilled = false;
					}
					$metaData->setFieldValue($element, $fieldName, $this->request->get($fieldName));
				}
			}
			if($allFieldsFilled){
				$this->em->persist($element);
				$this->em->flush();
				return $this->redirectRoute("admin-entity-update", ["entityName" => $this->entityAdmin->getName(), "id"=>$element->getId()]);
			}else{
				$msg = "Echec... Remplissez tous les champs";
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "add",
			"entityAdmin" => $this->entityAdmin->getName(), 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"fields" => $fields,
			"element" => $element,
			"msg"=>$msg
		]);
	}

	public function update(){
		
		$fields = $this->entityAdmin->getFields($this->em);
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($this->request->get("id"));
		$msg = "";
		if($this->request->getMethod() == "POST"){
			$metaData = $this->entityAdmin->getMetaData($this->em);
			$allFieldsFilled = true;
			foreach($fields as $fieldName => $type){
				if($this->request->get($fieldName) !== null){
					if(!$metaData->isNullable($fieldName) && trim($this->request->get($fieldName)) == ""){
						$allFieldsFilled = false;
					}
					$metaData->setFieldValue($element, $fieldName, $this->request->get($fieldName));
				}
			}
			if($allFieldsFilled){
				$this->em->merge($element);
				$this->em->flush();
				$msg = "Modification effectuée";
			}else{
				$msg = "Echec... Remplissez tous les champs";
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "update",
			"entityAdmin" => $this->entityAdmin->getName(), 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"fields" => $fields,
			"element" => $element,
			"msg"=>$msg
		]);
	}

	public function delete(){
		
		$fields = $this->entityAdmin->getFields($this->em);
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($this->request->get("id"));
		$this->em->remove($element);
		$this->em->flush();
		return $this->redirectRoute("admin-entity-list", ["entityName" => $this->entityAdmin->getName()]);
	}
	

}