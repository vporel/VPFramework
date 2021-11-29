<?php

namespace VPFramework\Console;

class Console
{

    /**
     * To manage any command in console
     * @param array $associations Structure [ ["commandName" => ["class" => "...", "description" => "..."]] ]
     */
    public static function manageConsole(array $associations){
        global $argv;
        if(count($argv) > 1){

        
            for($i = 0; $i<count($argv);$i++){
                $argv[$i] = strtolower($argv[$i]);
            }
            $commandName = $argv[1];
        
            if(array_key_exists($commandName, $associations)){
                $command = new $associations[$commandName]["class"](array_slice($argv, 2, count($argv)));
                $command->execute();
            }else{
                echo "\nThe command '$commandName' doesn't exist\n";
            }
        }else{
            foreach($associations as $command => $infos){
                echo $command." - ".$infos["description"];
            }
        }
    }

    public static function input($message = "", $default = ""){
        echo "\n".$message." ";
        $var = "";
        $var = fgets(STDIN);
        $var = trim($var);
        if($var == "")
            return $default;
        return $var;
    }

    public static function createFile($path, $content){
        try{            
            $file = fopen($path, "w+");
            fputs($file, $content);
            fclose($file);
            echo "\t- The file $path has been successfully created\n";
            return true;;
        }catch(\Exception $e){
            echo $e->getMessage();
            echo "\nThe creation of $path has failed\n";
            return false;
        }
    }
}