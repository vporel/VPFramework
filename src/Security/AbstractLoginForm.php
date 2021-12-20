<?php

namespace VPFramework\Security;

use VPFramework\Doctrine\Repository;
use VPFramework\Form\AbstractFormUnique;

abstract class AbstractLoginForm extends AbstractFormUnique
{
    private $repository;

    public function __construct($object, Repository $repository)
    {
        parent::__construct($object);
        $this->repository = $repository;
    }

    public function isAuthenticated()
    {
        if ($this->isSubmitted() && $this->isValid()) {
            $keyField = $this->object->getKeyField();
            $object = $this->repository->findOneBy([$keyField => $this->parameters[$keyField]]);
            if ($object != null) {
                foreach ($this->fields as $field) {
                    $getter = 'get'.ucfirst($field->getName());
                    if ($field->getName() != $keyField && $object->$getter() != $field->getRealValue($this->parameters[$field->getName()])) {
                        $this->error = 'Identifiants inccorects';

                        return false;
                    }
                }
                $this->object = $object;
                $this->authenticate();

                return true;
            } else {
                $this->error = 'Utilisateur inexistant';

                return false;
            }
        }

        return false;
    }

    public function authenticate()
    {
        if ($this->object->getId() != null) {
            $_SESSION['user'] = [];
            $_SESSION['user']['id'] = $this->object->getId();
            $_SESSION['user']['repository'] = get_class($this->repository);
        } else {
            throw ("Authentification impossible : Lobject ne possède pas d'identifiant");
        }
    }
}
