<?php

namespace VPFramework\Form;

abstract class FormMultipleEntity
{
    public abstract function getManagedData();

    public function getHTMLData()
    {
        $managedData = $this->getManagedData();
        $data = '';
        if(!isset($managedData["id"]))
            $data .= 'data-id="'.$this->id.'"';
        foreach($managedData as $key => $value)
            $data .= 'data-'.$key.'="'.$value.'"';
        return $data;
    }
}