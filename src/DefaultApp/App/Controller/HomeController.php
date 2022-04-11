<?php
namespace VPFramework\DefaultApp\App\Controller;

use VPFramework\Core\Controller;

class HomeController extends Controller
{

	public function index(){
		return $this->render("home.php");
	}
}