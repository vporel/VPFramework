<?php

namespace VPFramework\Form\Field;

use VPFramework\Form\Field\Field;

class File extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
        $this
            ->addOption("extensions", ["jpg", "png", "gif", "jpeg"])
            ->addOption("destFolder", ["."])
            ->addOption("save", null) //Accepted values : all_raw, all_new, name_raw, name_new, extension
            ->addOption("namePrefix", "") 
            ->addOption("nameSuffix", "");
        parent::__construct($label, $name, $options);        
    }

    public function getFieldHTML(){
        return '<input type="file" name="'.$this->name.'" class="form-control" id="'.$this->name.'" accept=".'.implode(',.', $this->getExtensions()).'"/>';
    }

    public function uploadFile($newName = null, $key = null, $object = null)
    {
        if($key == null)
            $key = $this->name;
        if(isset($_FILES[$key]["name"]) && $_FILES[$key]["name"] != ""){ 
            $pathinfo = pathinfo($_FILES[$key]["name"]);
            $extension = strtolower($pathinfo["extension"]);
            if(in_array($extension, $this->getExtensions())){
                $entireName = $this->getNamePrefix().$newName.$this->getNameSuffix();
                if(strpos($entireName, "/")> 0 && !file_exists($this->getDestFolder()."/".substr($entireName, 0, strpos($entireName, "/"))))
                    mkdir($this->getDestFolder()."/".substr($entireName, 0, strpos($entireName, "/")), 0777, true);
                $newBaseName = ($newName == null) ? $_FILES[$key]["name"] : $this->getNamePrefix().$newName.$this->getNameSuffix().".".$extension;
                if(move_uploaded_file($_FILES[$key]["tmp_name"], $this->getDestFolder()."/$newBaseName")){
                    $save = $this->getSave();
                    if($save != null){
                        if($object != null){
                            $method = "set".ucfirst($this->name);
                            switch($save){
                                case "all_raw": $object->$method($pathinfo["basename"]);break;
                                case "all_new": $object->$method($newBaseName);break;
                                case "name_raw": $object->$method($pathinfo["filename"]);break;
                                case "name_new": $object->$method($newName);break;
                                case "extension": $object->$method($extension);break;
                                default: $object->$method($pathinfo["basename"]);break;
                            }
                        }else{
                            throw new \Exception("Impossible d'effectuer la sauvegarde car aucun object n'a été fourni");
                        }
                    }
                    return true;        
                }
            }
        }
        return false;
    }
}