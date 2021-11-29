<?php
    
namespace App\Controller;

use VPFramework\Core\Controller;

class ControllerHome extends Controller
{
    public function index()
    {
        return $this->render("home/index.html.php");
    }
}
