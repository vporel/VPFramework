<?php
use VPFramework\Service\Security\Rule;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Repository\AdminGroupPermissionRepository;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\DefaultApp\App\Repository\AdminGroupRepository;
use VPFramework\Service\Admin\EntityAdmin;

return [
    "security" => [
        new Rule("", ["^/admin(?!/login|/first-admin|/.+/jsonList)"], [Admin::class => []], "admin-login")
    ],
    "admin" => [
        new EntityAdmin(AdminRepository::class),
        new EntityAdmin(AdminGroupRepository::class),
        new EntityAdmin(AdminGroupPermissionRepository::class)
    ]
];
