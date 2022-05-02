<?php
use VPFramework\Core\Routing\Route;
use VPFramework\DefaultApp\App\Controller\AdminController;
use VPFramework\DefaultApp\App\Controller\EntityAdminController;

return [
    new Route("admin", AdminController::class, "index", "/admin"),

    new Route("admin-login", AdminController::class, "login", "/admin/login"),
    new Route("admin-first-admin", AdminController::class, "firstAdmin", "/admin/first-admin"),
    new Route("admin-update-password", AdminController::class, "updatePassword", "/admin/update-password"),
    new Route("admin-logout", AdminController::class, "logout", "/admin/logout"),

    new Route("admin-entity-list", EntityAdminController::class, "list", "/admin/{entityName}/list"),
    new Route("admin-entity-add", EntityAdminController::class, "add", "/admin/{entityName}/add"),
    new Route("admin-entity-update", EntityAdminController::class, "update", "/admin/{entityName}/{key}/update"),
    new Route("admin-entity-delete", EntityAdminController::class, "delete", "/admin/{entityName}/{key}/delete"),
    new Route("admin-entity-delete-many", EntityAdminController::class, "deleteMany", "/admin/{entityName}/delete-many"),
];