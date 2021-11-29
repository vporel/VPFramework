<?php

namespace VPFramework\Form\Field;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;


/**
 * Cette classe n'est pas utilisable dans un contexte différent de celui de VPFramework et de Doctrine
 * Car elle ne peut fonctionner sans les classes : Doctrine\ORM\EntityManager et VPFramework\Core\DIC;
 */

class Relation extends AbstractField
{
    private $repository;

    public function getEntityClass(){
        if(isset($this->options["entityClass"])){
            return $this->options["entityClass"];
        }else
            throw new \Exception("Aucune classe Entity passée dans les options pour la relation");
    }

    public function isNullable(){
        return $this->options["nullable"] ?? false;
    }

    public function getRepositoryClass(){
        if(isset($this->options["repositoryClass"])){
            return $this->options["repositoryClass"];
        }else
            throw new \Exception("Aucune classe Repository passée dans les options pour la relation");
    }

    public function getRepository(){
        if($this->repository == null){
            $this->repository = DIC::getInstance()->get($this->getRepositoryClass());
        }
        return $this->repository;  
    }

    public function getElementsJSON()
    {
        $array = [];
        if($this->isNullable())
            $array[] = ["value"=> 0, "text"=>"Aucun"];
        $elements = $this->getRepository()->findAll();
        $metadata = DIC::getInstance()->get(EntityManager::class)->getClassMetadata($this->getEntityClass());
        $associationFields = [];
        foreach($metadata->associationMappings as $field){
            if(isset($field["joinColumns"]))
                $associationFields[] = $field["fieldName"];
        }
        foreach($elements as $element){
            $option = [
                "value" => $element->getId(),
                "text" => $element->getName(),
            ];
            foreach($associationFields as $field){
                $method = "get".ucfirst($field);
                $option[$field] = $element->$method()->getId();
            }
            $array[] = $option;
        }
        return json_encode(["associationFields" => $associationFields, "elements" => $array]);
    }

    public function getFieldHTML(){
        $select = '
                <select name="'.$this->name.'">
        ';
        $elements = $this->getRepository()->findAll();
        foreach($elements as $element){
            $select .= '<option value="'.$element->getId().'" '.($element->getId() == $this->getDefault() ? 'selected' : '').'>'.$element->getName().'</option>';
        }

        $select .= '</select>';
        return $select;
    }

    public function getRealValue($value)
    { 
        if((int) $value > 0)
            return $this->getRepository()->find($value); 
        else{
            if(!$this->isNullable())
                throw new \Exception("La valeur pour un champ de type Relation doit être un id (entier). Ou alors définissez ce champ comme nullable");
            else
                return null;
        }
    }
}