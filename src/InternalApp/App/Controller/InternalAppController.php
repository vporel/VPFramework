<?php
namespace VPFramework\InternalApp\App\Controller;

use VPFramework\Core\Constants;
use VPFramework\Core\Controller;

define("ASSETS", __DIR__."/../../Assets");

abstract class InternalAppController extends Controller
{
    
	protected function getViewDir()
	{
		return Constants::FRAMEWORK_ROOT."/InternalApp/View";
	}
}