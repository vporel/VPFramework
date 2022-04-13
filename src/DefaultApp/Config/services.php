<?php
use VPFramework\Service\Security\Rule;
use VPFramework\DefaultApp\App\Entity\Admin;

return [
    "security" => [
        new Rule(["^/admin(?!/login|/first-admin)"], [Admin::class => []], "admin-login")
    ]
];
