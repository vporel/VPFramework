<?php
use VPFramework\Service\Security\Rule;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\Service\Admin\EntityAdmin;

return [
    "security" => [
        new Rule(["^/admin(?!/login|/first-admin)"], [Admin::class => []], "admin-login")
    ],
    "admin" => [
        new EntityAdmin(Admin::class, AdminRepository::class)
    ]
];
