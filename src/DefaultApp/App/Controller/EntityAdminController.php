<?php
namespace VPFramework\DefaultApp\App\Controller;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\DIC;
use VPFramework\Core\Request;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\Model\Entity\Annotations\Field;
use VPFramework\Model\Entity\Annotations\FileField;
use VPFramework\Model\Entity\Annotations\NumberField;
use VPFramework\Model\Entity\Annotations\PasswordField;
use VPFramework\Model\Entity\Annotations\RelationField;
use VPFramework\Model\Entity\Annotations\TextLineField;
use VPFramework\Utils\AnnotationReader;
use VPFramework\Utils\FileUpload;
use VPFramework\Utils\FileUploadException;

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
		return $this->render("admin/entity/list.php", [
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"fields" => $this->entityAdmin->getMainFields($this->em),
			"filterFields" => $this->entityAdmin->getFilterFields($this->em),
			"elements" => $elements
		]);
	}

	public function add(){
		
		$fields = $this->entityAdmin->getFields($this->em);
		$class = $this->entityAdmin->getEntityClass();
		$element = new $class();
		$msg = "";
		$continueAdd = false;
		if($this->request->getMethod() == "POST"){
			$result = $this->hydrateObject($element);
			$continueAdd = $this->request->get("continueAdd") ?? false;
			if($result == ""){
				$this->em->persist($element);
				$this->em->flush();
				if(!$continueAdd)
					return $this->redirectRoute("admin-entity-update", ["entityName" => $this->entityAdmin->getName(), "id"=>$element->getId()]);
				else{
					//Réinitialisation
					$msg = "Elément ajouté avec succès";
					if($this->entityAdmin->getEntityClass() == Admin::class)
						$element = new Admin(false);
					else
						$element = new $class();
				}
			}else{
				$msg = $result;
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "add",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"fields" => $fields,
			"element" => $element,
			"msg"=>$msg,
			"continueAdd"=> $continueAdd
		]);
	}

	public function update(){
		
		$fields = $this->entityAdmin->getFields($this->em);
		$element = DIC::getInstance()->get($this->entityAdmin->getRepositoryClass())->find($this->request->get("id"));
		$msg = "";
		if($this->request->getMethod() == "POST"){
			
			$result = $this->hydrateObject($element);
			if($result == ""){
				$this->em->merge($element);
				$this->em->flush();
				$msg = "Modification effectuée";
			}else{
				$msg = $result;
			}
		}
		return $this->render("admin/entity/add-update.php", [
			"mode" => "update",
			"entityAdmin" => $this->entityAdmin, 
			"entitiesAdmin" => $this->entitiesAdmin, 
			"adminGroupPermission" => $this->adminGroupPermission, 
			"fields" => $fields,
			"element" => $element,
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

	/**
	 * 
	 * @return string Chaine vide ("") si tous les éléments sont bien renseigné
	 */
	private function hydrateObject($object) :string
	{
		$fields = $this->entityAdmin->getFields($this->em);
		$metaData = $this->entityAdmin->getMetaData($this->em);
		$msg = "";
		foreach($fields as $field){
			//Gestion des fichiers envoyés
			$fieldName = $field["name"];
			if($fieldName != "id"){
				$type = $field["type"];
				$value = $this->request->get($fieldName);
				$customFieldAnnotation = $field["customAnnotation"];
				if($customFieldAnnotation instanceof FileField){
					try{
						$fileBaseName = FileUpload::upload($fieldName, $customFieldAnnotation->folder, $customFieldAnnotation->extensions); 
						if($fileBaseName != ""){
							$metaData->setFieldValue($object, $fieldName, $fileBaseName);
						}
					}catch(FileUploadException $e){
						if($e->getCode() == FileUploadException::FILE_NOT_RECEIVED && !$field["nullable"]){
							$msg = "Choisissez un fichier pour le champ <b>".$field["label"]."</b>";
						}elseif($e->getCode() == FileUploadException::WRONG_EXTENSION){
							$msg = "Champ <b>".$field["label"]."</b> invalide - <i>Mauvaise extension (".implode(", ", $customFieldAnnotation->extensions).")</i>";
						}
					}
				}elseif($customFieldAnnotation instanceof RelationField){
					if($value != null)
						$metaData->setFieldValue($object, $fieldName, $customFieldAnnotation->getRepository()->find($value));
					else{
						if(!$field["nullable"])
							$msg = "Renseignez le champ <b>".$field["label"]."</b>";
					}
				}elseif($customFieldAnnotation instanceof TextLineField){
					if(strlen($value) >= $customFieldAnnotation->minLength){
						if(strlen($value) <= $customFieldAnnotation->maxLength){
							if(preg_match($customFieldAnnotation->pattern, $value)){
								if($customFieldAnnotation instanceof PasswordField){
									$hashFunction = $customFieldAnnotation->hashFunction;
									$metaData->setFieldValue($object, $fieldName, $hashFunction($value));
								}else
									$metaData->setFieldValue($object, $fieldName, $value);
							}else{
								$msg = "<b>$fieldName</b> : ".$customFieldAnnotation->patternMessage;
								$metaData->setFieldValue($object, $fieldName, $value);
							}
						}else{
							$msg = "La longueur maximale pour <b>$fieldName</b> est : ".$customFieldAnnotation->maxLength;
							$metaData->setFieldValue($object, $fieldName, $value);
						}
					}else{
						$msg = "La longueur minimale pour <b>$fieldName</b> est : ".$customFieldAnnotation->minLength;
						$metaData->setFieldValue($object, $fieldName, $value);
					}
								
				}elseif($customFieldAnnotation instanceof NumberField){
					if((float) $value >= $customFieldAnnotation->min){
						if((float) $value <= $customFieldAnnotation->max){
							$metaData->setFieldValue($object, $fieldName, $value);
						}else{
							$msg = "La longueur maximale pour <b>$fieldName</b> est : ".$customFieldAnnotation->maxLength;
						}
					}else{
						$msg = "La valeur minimale pour <b>$fieldName</b> est : ".$customFieldAnnotation->minLength;
					}
								
				}elseif($type == "boolean"){
					if($value != null){
						$metaData->setFieldValue($object, $fieldName, $value);
					}else{
						$metaData->setFieldValue($object, $fieldName, false);
					}
				}else{
					if($value != null){
						if(!$metaData->isNullable($fieldName) && trim($value) == ""){
							
							$msg = "Renseignez le champ <b>".$field["label"]."</b>";
						}
						$metaData->setFieldValue($object, $fieldName, $value);
					}else{
						if(!$metaData->isNullable($fieldName)){
							$msg = "Renseignez le champ <b>".$field["label"]."</b>";
						}
					}
				}
			}
		}
		return $msg;
	}
	

}