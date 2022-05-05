<?php

namespace VPFramework\Console;

use VPFramework\Console\App\Create;

class Console
{

    /**
     * To manage any command in console
     */
    public static function run(){

        $commands = [
            "create" => [
                "class" => Create::class,
                "description" => "Creer un élément(controller, formulaire, ...) dans l'application"
            ]
        ];
        global $argv;
        if(count($argv) > 1){
                    
            for($i = 0; $i<count($argv);$i++){
                $argv[$i] = strtolower($argv[$i]);
            }
            $commandName = $argv[1];

            if($commandName != "help"){
            
                if(array_key_exists($commandName, $commands)){
                    $command = new $commands[$commandName]["class"](array_slice($argv, 2, count($argv)));
                    $command->execute();
                }else{
                    echo "\nLa commande '$commandName' n'existe pas\n";
                }
                return;
            }
        }
    
        //Affichage de la liste des commandes
        echo "Console - VPFramework\n";
        echo "--- Liste des commandes ---\n";
        foreach($commands as $command => $infos){
            echo "\t $command - ".$infos["description"]."\n";
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
        if(!file_exists($path)){
            try{            
                $file = fopen($path, "w+");
                fputs($file, $content);
                fclose($file);
                echo "\t- Le fichier $path a été crée avec succès\n";
                return true;;
            }catch(\Exception $e){
                echo $e->getMessage();
                echo "\nLa création de $path a échouée\n";
                return false;
            }
        }else{
            echo "Le fichier $path existe déjà";
        }
    }
}