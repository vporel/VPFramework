<?php

namespace VPFramework\Service\Security;

interface UserInterface
{
    /**
     * Le role que possède une instance de l'entité
     * @return string
     */
    public function getRole();

    /**
     * La clée permettant de vérifier l'authentification pour l'entité
     * Ex: username, email, phoneNumber
     */
    public function getKeyField();
}