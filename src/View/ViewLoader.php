<?php
namespace VPFramework\View;

use VPFramework\Core\DIC;


class ViewLoader
{
    private $viewDir;

    public function __construct($viewDir)
    {
        $this->viewDir = $viewDir;
        define("VIEW_DIR", $viewDir);
    }

    public function render(string $file, array $data = [])
    {
        $view = DIC::getInstance()->get(View::class);
        extract($view->getFunctions());
        extract($view->getGlobals());
        extract($data);
        $view = function($file){
            return VIEW_DIR."/".$file ;
        };
        require VIEW_DIR."/".$file;
    }

}