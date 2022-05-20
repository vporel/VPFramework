<?php
use VPFramework\Service\Security\Rule;
use VPFramework\InternalApp\App\Entity\Admin;
use VPFramework\InternalApp\App\Repository\AdminGroupPermissionRepository;
use VPFramework\InternalApp\App\Repository\AdminRepository;
use VPFramework\InternalApp\App\Repository\AdminGroupRepository;
use VPFramework\Service\Admin\EntityAdmin;

return [
    "security" => [
        new Rule("", ["^/admin(?!/login|/first-admin)"], [Admin::class => []], "admin-login")
    ],
    "admin" => [
        new EntityAdmin(AdminRepository::class),
        new EntityAdmin(AdminGroupRepository::class),
        new EntityAdmin(AdminGroupPermissionRepository::class)
    ]
];
