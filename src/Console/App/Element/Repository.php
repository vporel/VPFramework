<?php

namespace VPFramework\Console\App\Element;

use VPFramework\Console\Console;
use VPFramework\Core\Constants;

define("REPOSITORY_DIR", APP_ROOT."/App/Repository");
if(!is_dir(REPOSITORY_DIR))
    mkdir(REPOSITORY_DIR, 0777, true);

class Repository
{
    public static function create(){
        
        $entitiesClassesNames = Console::input("\tNom les classes entité [séparées par des espaces ](sans le namespace): ");
        if($entitiesClassesNames == ""){
            echo "\nOperation annulée\n";
            return;
        }
        $entitiesClassesNames = explode(" ", $entitiesClassesNames);
        foreach($entitiesClassesNames as $entityClassName){
            if(class_exists("App\Entity\\$entityClassName", true)){
    
                $repositoryPath = REPOSITORY_DIR."/".$entityClassName."Repository.php";
                Console::createFile($repositoryPath,  self::getCode($entityClassName));
            }else{
                echo "\nLa classe entité App\\Entity\\$entityClassName n'existe pas\n";
            }
        }
        

    }
    

    private static function getCode($entityClassName)
    {
//-------------------
$content = 
'<?php

namespace App\\Repository;

use App\\Entity\\'.$entityClassName.';
use VPFramework\Model\Repository\Repository;

class '.$entityClassName.'Repository extends Repository
{

    public function getEntityClass(){
        return '.$entityClassName.'::class;
    }
}
';
//--------------------
        return $content;
    }

}