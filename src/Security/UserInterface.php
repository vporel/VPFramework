<?php

namespace VPFramework\Security;

interface UserInterface
{
    public function getRole();

    public function getKeyField();
}