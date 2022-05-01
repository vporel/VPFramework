<?php
namespace VPFramework\DefaultApp\App\Controller;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\Form\Form;
use VPFramework\Model\Entity\Annotations\Field;
use VPFramework\Model\Entity\Annotations\FileField;
use VPFramework\Model\Entity\Annotations\NumberField;
use VPFramework\Model\Entity\Annotations\PasswordField;
use VPFramework\Model\Entity\Annotations\RelationField;
use VPFramework\Model\Entity\Annotations\TextLineField;
use VPFramework\Model\Entity\Entity;
use VPFramework\Utils\AnnotationReader;
use VPFramework\Utils\FileUpload;
use VPFramework\Utils\FileUploadException;
use VPFramework\Utils\ObjectReflection;

class EntityAdminController extends DefaultAppController
{
	private $em, $entitiesAdmin, $entityAdmin, $request, $adminGroupPermission;
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
	}

	public function list(){
		$elements = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->findBy([], ["-id"]);
		
		$form = new Form("entity-add-form", $this->entityAdmin->getEntityClass());
		return $this->render("admin/entity/list.php", [
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"formFields" => $form->getFields(),
			"mainFields" => $this->entityAdmin->getMainFields(),
			"filterFields" => array_keys($this->entityAdmin->getFilterFields()),
			"elements" => $elements
		]);
	}

	public function add(){
		
		$fields = $this->entityAdmin->getFields();
		$class = $this->entityAdmin->getEntityClass();
		$element = new $class();
		$form = new Form("entity-add-form", $this->entityAdmin->getEntityClass(), $element);

		$msg = "";
		$continueAdd = false;
		if($form->isSubmitted() && $form->isValid()){
			$form->updateObject();
			$continueAdd = $this->request->get("continueAdd") ?? false;
			$this->em->persist($element);
			$this->em->flush();
			if(!$continueAdd)
				return $this->redirectRoute("admin-entity-update", ["entityName" => $this->entityAdmin->getName(), "id"=>$element->getId()]);
			else{
				//Réinitialisation
				$msg = "Elément ajouté avec succès";
				$element = new $class();
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "add",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"fields" => $fields,
			"element" => $element,
			"form" => $form,
			"msg"=>$msg,
			"continueAdd"=> $continueAdd
		]);
	}

	public function update(){
		
		$fields = $this->entityAdmin->getFields();
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($this->request->get("id"));
		
		$form = new Form("entity-add-form", $this->entityAdmin->getEntityClass(), $element);
		$msg = "";
		if($form->isSubmitted() && $form->isValid()){
			$form->updateObject();
			$this->em->merge($element);
			$this->em->flush();
			$msg = "Modification effectuée";
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "update",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"fields" => $fields,
			"element" => $element,
			"form" => $form,
			"msg"=>$msg
		]);
	}

	public function delete(){
		
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($this->request->get("id"));
		$this->em->remove($element);
		$this->em->flush();
		return $this->redirectRoute("admin-entity-list", ["entityName" => $this->entityAdmin->getName()]);
	}

	public function deleteMany(){
		$ids = explode("-", $this->request->get("ids"));
		foreach($ids as $id){
			$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($id);
			$this->em->remove($element);
		}
		$this->em->flush();
		return $this->redirectRoute("admin-entity-list", ["entityName" => $this->entityAdmin->getName()]);
	}
	

}