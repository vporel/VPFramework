<?php
use VPFramework\Service\Security\Rule;
use VPFramework\DefaultApp\App\Entity\Admin;

return [
    "security" => [
        new Rule(["^/admin(?!/login)"], [Admin::class => []], "admin-login")
    ]
];
