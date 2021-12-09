<?php

namespace VPFramework\Security;

use VPFramework\Form\AbstractFormUnique;

abstract class AbstractLoginForm extends AbstractFormUnique
{
    public function isAuthenticated()
    {
        if($this->isSubmitted() && $this->isValid()){
            $keyField = $this->object->getKeyField();
            $criteria = [];
            $object = $this->repository->findOneBy([$this->object->getKeyField() => $this->parameters[$this->object->getKeyField()]]);
            if($object != null){
                
                foreach($this->fields as $field){
                    $getter = "get".ucfirst($field->getName());
                    if($field->getName() != $this->object->getKeyField() && $object->$getter() != $field->getRealValue($this->parameters[$field->getName()])){
                        $this->error = "Identifiants inccorects";
                        return false;
                    }
                }
                $this->object = $object;
                $this->authenticate();
                return true;
            }else{
                $this->error = "Utilisateur inexistant";
                return false;
            }
        }
        return false;
    }
    public function authenticate(){
        if($this->object->getId() != null){
            $_SESSION["user"] = [];
            $_SESSION["user"]["id"] = $this->object->getId();
            $_SESSION["user"]["repository"] = $this->repositoryClass;
        }else{
            throw("Authentification impossible : Lobject ne possède pas d'identifiant");
        }
    }
}