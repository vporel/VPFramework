<?php
use VPFramework\Core\Routing\RouteGroup;
use VPFramework\Core\Routing\RouteInGroup;

return [
    (new RouteGroup("AdminController", "/admin", [
        new RouteInGroup("admin", "index", ""),

        new RouteInGroup("admin-login", "login", "/login"),
        new RouteInGroup("admin-first-admin", "firstAdmin", "/first-admin"),
        new RouteInGroup("admin-update-password", "updatePassword", "/update-password"),
        new RouteInGroup("admin-logout", "logout", "/logout"),
    ]))->setControllerNamespace("VPFramework\\DefaultApp\\App\\Controller"),
    (new RouteGroup("EntityAdminController", "/admin/<entityName>", [
        new RouteInGroup("admin-entity-list", "list", "/list"),
        new RouteInGroup("admin-entity-jsonList", "jsonList", "/jsonList"),
        new RouteInGroup("admin-entity-add", "add", "/add"),
        new RouteInGroup("admin-entity-update", "update", "/<key>/update"),
        new RouteInGroup("admin-entity-delete", "delete", "/<key>/delete"),
        new RouteInGroup("admin-entity-delete-many", "deleteMany", "/delete-many"),
    ]))->setControllerNamespace("VPFramework\\DefaultApp\\App\\Controller"),
];