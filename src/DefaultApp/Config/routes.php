<?php
use VPFramework\Core\Routing\Route;

return [
    new Route("admin", "AdminController:index", "/admin"),

    new Route("admin-login", "AdminController:login", "/admin/login"),
    new Route("admin-first-admin", "AdminController:firstAdmin", "/admin/first-admin"),
    new Route("admin-update-password", "AdminController:updatePassword", "/admin/update-password"),
    new Route("admin-logout", "AdminController:logout", "/admin/logout"),

    new Route("admin-entity-list", "EntityAdminController:list", "/admin/{entityName}/list"),
    new Route("admin-entity-jsonList", "EntityAdminController:jsonList", "/admin/{entityName}/jsonList"),
    new Route("admin-entity-add", "EntityAdminController:add", "/admin/{entityName}/add"),
    new Route("admin-entity-update", "EntityAdminController:update", "/admin/{entityName}/{key}/update"),
    new Route("admin-entity-delete", "EntityAdminController:delete", "/admin/{entityName}/{key}/delete"),
    new Route("admin-entity-delete-many", "EntityAdminController:deleteMany", "/admin/{entityName}/delete-many"),
];