<?php
namespace VPFramework\View;

use VPFramework\Core\DIC;

class ViewLoader
{
    private $viewDir;

    public function __construct()
    {

    }

    public function render(string $file, array $data = [])
    {
        $_VIEW = DIC::getInstance()->get(View::class);
        extract($_VIEW->getGlobals());
        extract($data);
        require VIEW_DIR."/".$file;
    }

}