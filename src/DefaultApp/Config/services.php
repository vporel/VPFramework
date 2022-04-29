<?php
use VPFramework\Service\Security\Rule;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Entity\AdminGroup;
use VPFramework\DefaultApp\App\Entity\AdminGroupPermission;
use VPFramework\DefaultApp\App\Repository\AdminGroupPermissionRepository;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\DefaultApp\App\Repository\AdminGroupRepository;
use VPFramework\Service\Admin\EntityAdmin;

return [
    "security" => [
        new Rule("", ["^/admin(?!/login|/first-admin)"], [Admin::class => []], "admin-login")
    ],
    "admin" => [
        new EntityAdmin(Admin::class, AdminRepository::class),
        new EntityAdmin(AdminGroup::class, AdminGroupRepository::class),
        new EntityAdmin(AdminGroupPermission::class, AdminGroupPermissionRepository::class)
    ]
];
