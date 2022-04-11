<?php
use VPFramework\Core\Routing\Route;
use VPFramework\DefaultApp\App\Controller\AdminController;

return [
    new Route("admin", AdminController::class, "index", "/admin"),
    new Route("adminLogin", AdminController::class, "login", "/login-admin"),
];