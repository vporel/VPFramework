<?php

namespace VPFramework\Console\App\Element;

use VPFramework\Console\Console;

const CONTROLLER_DIR = ROOT."/app/Controller";
if(!is_dir(CONTROLLER_DIR))
    mkdir(CONTROLLER_DIR, 0777, true);

class Controller
{
    public static function create(){
        $controllersNames = Console::input("\tEnter the controller's name(s) (separate with blank to create many): ");
        
        $controllersNames = explode(" ", $controllersNames);
        if(count($controllersNames) > 1){
            echo "The following files are going to be created :\n\t ".implode(".php\n\t", $controllersNames).".php";
            $answer = strtolower(Console::input("\nAre you OK with this?[yes] : ", "yes"));
            if($answer == "yes"){
                foreach($controllersNames as $controllerName){
                    Console::createFile(CONTROLLER_DIR."/".$controllerName.".php", Controller::getCode($controllerName));
                }
            }else{
                echo "Operation canceled";
            }
        }else{
            $functions = Console::input("\tDo you have any functions to add ?[] (separate with blank to create many): ");
            echo "The following file is going to be created : ".$controllersNames[0].".php";
            $answer = strtolower(Console::input("\nAre you OK with this?[yes] : ", "yes"));
            if($answer == "yes"){
                Console::createFile(CONTROLLER_DIR."/".$controllersNames[0].".php", Controller::getCode($controllersNames[0], explode(" ", $functions)));
            }else{
                echo "Operation canceled";
            }
        }
        
    }
    
   
    public static function getCode($controllerName, $functions = [])
    {
$code = "<?php
    
namespace App\Controller;

use VPFramework\Core\Controller;

class $controllerName extends Controller
{";
foreach($functions as $function){
$code .= "
    public function $function()
    {

    }
";
}
$code .= "}
";

        return $code;
    }

}