<?php

namespace VPFramework\Console\App\Element;

use VPFramework\Console\Console;
use VPFramework\Core\Constants;

define("FORM_DIR", APP_ROOT."/app/Form");
if(!is_dir(FORM_DIR))
    mkdir(FORM_DIR, 0777, true);

const FORM_FIELD_NAMESPACE = "VPFramework\\Form\\Field";
const FORM_FIELD_TYPES = [
    "textline" => "TextLine",
    "number" => "Number",
    "password" => "Password",
    "relation" => "Relation",
    "select" => "Select",
    "textarea" => "TextArea",
    "file" => "File",
    "email" => "Email",
];

function showFieldsList(){
    foreach(array_keys(FORM_FIELD_TYPES) as $type){
        echo "\t- ".$type."\n";
    }
}

class Form
{
    public static function create(){
        $formName = ucfirst(Console::input("\tEnter the form's name : "));
        $fields = [];
        echo "Let's create some fields for your form\n";
        do{
            $field = self::getField();
            if($field["name"] != "")
                $fields[] = $field;
                echo "An other field\n";
        }while($field["name"] != "");
        if(count($fields) == 0){
            echo "There is no field. Operation canceled";
        }else{
            echo "The following file is going to be created : ".$formName.".php";
            $answer = strtolower(Console::input("\nAre you OK with this?[yes] : ", "yes"));
            if($answer == "yes"){
                Console::createFile(FORM_DIR."/".$formName.".php", self::getCode($formName, $fields));
            }else{
                echo "Operation canceled";
            }
        }
        
    }

    public static function getField()
    {
        $field = [];
        echo "Name :";
        $field["name"] = Console::input();
        if($field["name"] != ""){
            echo "Label :";
            $field["label"] = Console::input();
            do{
                echo "Type (? to show types list, default = textline) :";
                $field["type"] = strtolower(Console::input("","textline"));
                if($field["type"] == "?"){
                    showFieldsList();
                }else if(!in_array($field["type"], array_keys(FORM_FIELD_TYPES))){
                    echo "Type inconnu\n";
                }
            }while($field["type"] == "?" || !in_array($field["type"], array_keys(FORM_FIELD_TYPES)));
        }
        return $field;
    }
    
   
    public static function getCode($formName, $fields)
    {
$code = "<?php
    
namespace App\Form;

use VPFramework\Form\Form;";
//namespaces
$classesAdded = [];
foreach($fields as $field){
    if(!in_array($field["type"], $classesAdded)){
$code .= "
use ".FORM_FIELD_NAMESPACE."\\".FORM_FIELD_TYPES[$field["type"]].";";
        $classesAdded[] = $field["type"];
    }
}
$code .= '
class '.$formName.' extends Form{

    public function build(){
        $this';
foreach($fields as $field){
$code .= '
            ->addField(new '.FORM_FIELD_TYPES[$field["type"]].'("'.$field["label"].'", "'.$field["name"].'", $options = []))';
}
$code .= ';
    }
}
';

        return $code;
    }

}