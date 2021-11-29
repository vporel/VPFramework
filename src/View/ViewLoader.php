<?php
namespace VPFramework\View;

use VPFramework\Core\DIC;

class ViewLoader
{
    private $viewDir;

    public function __construct($viewDir)
    {
        $this->viewDir = $viewDir;        
    }

    public function render(string $file, array $data = [])
    {
        $_VIEW = DIC::getInstance()->get(View::class);
        define("VIEW_DIR", $this->viewDir);
        extract($_VIEW->getGlobals());
        extract($data);
        require $this->viewDir."/".$file;
    }

}