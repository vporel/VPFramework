<?php
namespace VPFramework\DefaultApp\App\Controller;

use VPFramework\Core\Constants;
use VPFramework\Core\Controller;

abstract class DefaultAppController extends Controller
{
    
	protected function getViewDir()
	{
		return Constants::FRAMEWORK_ROOT."/DefaultApp/View";
	}
}