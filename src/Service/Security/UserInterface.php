<?php

namespace VPFramework\Service\Security;

interface UserInterface
{
    /**
     * Le role que possède l'instance de l'entité
     * @return string
     */
    public function getRole();

}