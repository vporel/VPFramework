<?php

namespace VPFramework\Core\Routing\Security;

interface UserInterface
{
    /**
     * Les roles que possède une instance de l'entité
     * @return array
     */
    public function getRoles();

    /**
     * La clée permettant de vérifier l'authentification pour l'entité
     * Ex: username, email, phoneNumber
     */
    public function getKeyField();
}