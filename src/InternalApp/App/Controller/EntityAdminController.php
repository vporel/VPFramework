<?php
namespace VPFramework\InternalApp\App\Controller;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\Form\Field\File;
use VPFramework\Form\Field\Relation;
use VPFramework\Form\Form;
use VPFramework\Model\Entity\Entity;
use VPFramework\Utils\ClassUtil;

class EntityAdminController extends InternalAppController
{
	private $em, $entitiesAdmin, $entityAdmin, $request, $adminGroupPermission;
	private $keyProperty, $orderField;
	public function __construct(EntityManager $em, ServiceConfiguration $serviceConfig, Request $request){
		$this->em = $em;
		$this->request = $request;
		$this->entitiesAdmin = $serviceConfig->getService("admin");
		foreach($this->entitiesAdmin as $entityAdmin){
			if($entityAdmin->getName() == $this->request->get("entityName"))
				$this->entityAdmin = $entityAdmin;
		}
		if(
			$this->entityAdmin == null || 
			($this->adminGroupPermission = $this->getUser()->getPermission($this->entityAdmin->getEntityClass())) == null
		){
			$this->redirectRoute("admin");
		}
		$this->keyProperty = Entity::getEntityKeyProperty($this->entityAdmin->getEntityClass());
		$this->orderField = Entity::getEntityNaturalOrderField($this->entityAdmin->getEntityClass());
	}

	public function list(){
		$elements = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findBy([], [$this->orderField]);
		
		$form = new Form("entity-add-form", $this->entityAdmin->getEntityClass());
		return $this->render("admin/entity/list.php", [
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"keyProperty" => $this->keyProperty,
			"formFields" => $form->getFields(),
			"mainFields" => $this->entityAdmin->getMainFields(),
			"filterFields" => array_keys($this->entityAdmin->getFilterFields()),
			"elements" => $elements
		]);
	}

	public function add(){
		
		$class = $this->entityAdmin->getEntityClass();
		$element = new $class();
		$form = new Form("entity-add", $class, $element, array_keys($this->entityAdmin->getFields()));
		foreach($form->getFields() as $field){
			if($field instanceof Relation){
				$relatedEntityName = ClassUtil::getSimpleName($field->getEntityClass());
				$field->setLinkToAdd("/admin/".$relatedEntityName."/add");
			}
		}
		$msg = "";
		$continueAdd = false;
		if($form->isSubmitted() && $form->isValid()){
			$form->updateObject();
			$continueAdd = $this->request->get("continueAdd") ?? false;
			$this->em->persist($element);
			$this->em->flush();
			if(!$continueAdd){
				$keyProperty = $this->keyProperty;
				return $this->redirectRoute("admin-entity-update", ["entityName" => $this->entityAdmin->getName(), "key"=>$element->$keyProperty]);
			}else{
				//Réinitialisation
				$msg = "Elément ajouté avec succès";
				$form->setObject(new $class());
				$form->setParameters([]);
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "add",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"element" => $element,
			"keyProperty" => $this->keyProperty,
			"form" => $form,
			"msg"=>$msg,
			"continueAdd"=> $continueAdd
		]);
	}

	public function update(){
		
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findOneBy([$this->keyProperty => $this->request->get("key")]);
		
		$form = new Form("entity-update", $this->entityAdmin->getEntityClass(), $element, array_keys($this->entityAdmin->getFields()));
		foreach($form->getFields() as $field){
			if($field instanceof File)
				$field->setNullable(true);
		}
		if(!$this->adminGroupPermission->canUpdate) 
			$form->setFormReadOnly(true);
		$msg = "";
		if($form->isSubmitted() && $form->isValid()){
			$form->updateObject();
			$this->em->flush();
			$msg = "Modification effectuée";
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "update",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"keyProperty" => $this->keyProperty,
			"element" => $element,
			"form" => $form,
			"msg"=>$msg
		]);
	}

	public function delete(){
		
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findOneBy([$this->keyProperty => $this->request->get("key")]);
		$this->em->remove($element);
		$this->em->flush();
		return $this->redirectRoute("admin-entity-list", ["entityName" => $this->entityAdmin->getName()]);
	}

	public function deleteMany(){
		$keys = explode("-", $this->request->get("keys"));
		foreach($keys as $key){
			$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findOneBy([$this->keyProperty => $key]);
			$this->em->remove($element);
		}
		$this->em->flush();
		return $this->redirectRoute("admin-entity-list", ["entityName" => $this->entityAdmin->getName()]);
	}
	

}