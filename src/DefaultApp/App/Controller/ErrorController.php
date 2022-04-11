<?php
namespace VPFramework\DefaultApp\App\Controller;

use VPFramework\Core\Controller;

class ErrorController extends Controller
{
    public function accessDenied(){
		return $this->render("accessDenied.php");
	}
}