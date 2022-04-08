<?php

namespace VPFramework\Form;

use VPFramework\Core\DIC;
use VPFramework\View\View;

abstract class FormMultipleEntity implements \Serializable
{
    public static function getView(){
        return DIC::getInstance()->get(View::class);
    }

    public function getHTMLData()
    {
        $managedData = $this->serialize();
        $data = '';
        if(!isset($managedData["id"]))
            $data .= 'data-id="'.$this->id.'"';
        foreach($managedData as $key => $value)
            $data .= 'data-'.$key.'="'.$value.'"';
        return $data;
    }

    public function unserialize($data)
    {
        
    }
}