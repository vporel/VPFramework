<?php

use VPFramework\InternalApp\App\Controller\AdminController;
use VPFramework\InternalApp\App\Controller\EntityAdminController;
use VPFramework\InternalApp\App\Controller\EntityController;
use VPFramework\Routing\Route;
use VPFramework\Routing\RouteGroup;
use VPFramework\Routing\RouteInGroup;

return [
    new Route("entity-json-list", EntityController::class, "jsonList", "/entityJsonList"),
    new RouteGroup(AdminController::class, "/admin", [
        new RouteInGroup("admin", "index", ""),

        new RouteInGroup("admin-login", "login", "/login"),
        new RouteInGroup("admin-first-admin", "firstAdmin", "/first-admin"),
        new RouteInGroup("admin-update-password", "updatePassword", "/update-password"),
        new RouteInGroup("admin-logout", "logout", "/logout"),
    ]),
    new RouteGroup(EntityAdminController::class, "/admin/<entityName>", [
        new RouteInGroup("admin-entity-list", "list", "/list"),
        new RouteInGroup("admin-entity-add", "add", "/add"),
        new RouteInGroup("admin-entity-update", "update", "/<key>/update"),
        new RouteInGroup("admin-entity-delete", "delete", "/<key>/delete"),
        new RouteInGroup("admin-entity-delete-many", "deleteMany", "/delete-many"),
    ]),
];