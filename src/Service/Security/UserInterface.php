<?php

namespace VPFramework\Service\Security;

interface UserInterface
{
    public function getRole();

    public function getKeyField();
}