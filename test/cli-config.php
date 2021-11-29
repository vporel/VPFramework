<?php
// cli-config.php

use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;

require_once "vendor/autoload.php";

$DIC = DIC::getInstance();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($DIC->get(EntityManager::class));
