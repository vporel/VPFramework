<?php
namespace VPFramework\InternalApp\App\Controller;

use VPFramework\Core\Controller;

class ErrorController extends Controller
{
    public function accessDenied(){
		return $this->render("accessDenied.php");
	}
}