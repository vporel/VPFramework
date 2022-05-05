<?php

namespace VPFramework\Console\App\Element;

use VPFramework\Console\Console;
use VPFramework\Core\Constants;

const FIELD_TYPES = [ "integer", "string", "text", "float", "datetime", "relation"];
define("ENTITY_DIR", APP_ROOT."/app/Entity");
define("REPOSITORY_DIR", APP_ROOT."/app/Repository");
if(!is_dir(ENTITY_DIR))
    mkdir(ENTITY_DIR, 0777, true);
if(!is_dir(REPOSITORY_DIR))
    mkdir(REPOSITORY_DIR, 0777, true);

class Entity
{
    public static function create(){
        $entityName = ucfirst(Console::input("\tEnter the entity's name : "));
        /** Ask for entity's data to the user */
        $tableName = strtolower(Console::input("\tEnter the table's name : "));
        $fields = [];
        $usedClasses = [];
        //Creation of some fields
        echo "\nLet's create some fields for your entity...(To cancel, tap return)\n";
        $field = [];
        do{
            $field["name"] = Console::input("Enter the field's name : ");
            if($field["name"] != ""){
                do{
                    $field["type"] = strtolower(Console::input("Enter the type (string) :\nTap ? to show the types list\n ", "string"));
                    if($field["type"] == "?"){
                        self::showTypesList();
                    }elseif(in_array($field["type"], FIELD_TYPES)){
                        if($field["type"] == "relation"){
                            
                            echo "\nYou are going to create to create a relation between $entityName and an other entity";
                            $relatedEntityName = ucfirst($field["name"]);
                            if(file_exists(ENTITY_DIR."/".$relatedEntityName.".php")){
                                echo "\nChoose the relation type : ";
                                $relationType = self::getRelationType($entityName, $relatedEntityName);
                                if($relationType != null){
                                    $field["type"] = [$relationType, $relatedEntityName];
                                    $usedClasses[] = "App\\Entity\\".$relatedEntityName;
                                }else{
                                    $field["type"] = "";
                                    continue;
                                }
                            }else{
                                echo "\n\tThe entity $relatedEntityName doesn't exist, so you are not able to create a relation. First, create the Entity $relatedEntityName \n";
                                $field["name"] = "";
                                break;
                            }
                        }
                        $nullable = Console::input("Can this field be null ? (no)", "no");
                        if($nullable == "yes")
                            $field["nullable"] = "true";
                        else
                            $field["nullable"] = "false";
                        $fields[] = $field;
                        echo "\nLet's create another field for your entity...(To cancel, tap return)\n";
                    }
                }while($field["type"] == "?" || (!in_array($field["type"], FIELD_TYPES) && !is_array($field["type"])));
            }
        }while($field["name"] != "");
        if(count($fields) > 0){
            
            echo "\nRESULT : \n";
            $entityContent = 
                self::getEntityFileHead($entityName, $tableName, $usedClasses).
                self::getEntityFileBody($entityName, $fields).
                self::getEntityFileEnd();
            Console::createFile(ENTITY_DIR."/".$entityName.".php",  $entityContent);
            $repositoryContent = self::getRepositoryFileContent($entityName);
            if(!file_exists(REPOSITORY_DIR."/".$entityName."Repository.php")){
                Console::createFile(REPOSITORY_DIR."/".$entityName."Repository.php",  $repositoryContent);
            }else{
                echo "\nThe repository for this entity already exists\n";
            }
        }else{
            echo "\nOperation canceled\n";
            return false;
        }

    }
    
    public static function showTypesList()
    {
        echo "
            List of fields types : \n
                Simple :\n
                    - interger : Entire number\n
                    - string : Text with a max length of 255 characters\n
                    - text : Text with no max length\n
                    - float : Floating number\n\n
                \nDate and time :\n
                    \t\t- datetime
                \nRelations :\n
                    \t\t- relation : To use a other entity like a field for this one
        ";
    }

    public static function getRelationType($entityName, $relatedEntityName)
    {
        $relations = [
            1 => [
                "name"=>"ManyToOne",
                "description" => "One $relatedEntityName can have many $entityName"
            ],
            2 => [
                "name"=>"OneToMany",
                "description" => "One $entityName can have many $relatedEntityName"
            ],
            3 => [
                "name"=>"OneToOne",
                "description" => "One $entityName can have only one $relatedEntityName"
            ],
            4 => [
                "name"=>"ManyToMany",
                "description" => "One $entityName can have many $relatedEntityName and one $relatedEntityName can have many $entityName"
            ]
        ];
        echo "\n";
        foreach($relations as $nb => $relation){
            echo "\t".$nb.". ".$relation["name"]." : ".$relation["description"]."\n";
        }
        $nb = -1;
        while(!in_array($nb, [0, 1, 2, 3, 4])){
            $nb = (int) Console::input("\tEnter the number (0 to chose another type): ");
        }
        if($nb == 0)
            return null;
        else 
            return $relations[$nb]["name"];

    }

    private static function getEntityFileHead($entityName, $tableName, $usedClasses = [])
    {
//-------------------
$head = 
'<?php

namespace App\\Entity;

use Doctrine\\ORM\\Mapping as ORM;
';

foreach($usedClasses as $class)
        $head .= "use $class;\n";

$head .= 
'/**
 * @ORM\Entity
 * @ORM\Table(name="'.$tableName.'")
 */
class '.$entityName.'
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
  
';
//--------------------
        return $head;
    }

    private static function getEntityFileBody($entityName, $fields)
    {
$body = '';

foreach($fields as $field)
{
if(!is_array($field["type"])){
$body .= 
'
    /**
     * @ORM\\Column(type="'.$field["type"].'", nullable = '.$field["nullable"].')
     */
    private $'.$field["name"].';
';
}else{
$body .= 
'
    /**
     * @ORM\\'.$field["type"][0].'(targetEntity="'.$field["type"][1].'", inversedBy = "'.$entityName.'")
     * @ORM\JoinColumn(name="'.$field["name"].'_id", referencedColumnName="id", nullable = '.$field["nullable"].')
     */
    private $'.$field["name"].';
';    
}
}

$body .= 
'
    public function __construct(){
';
foreach($fields as $field){
    if($field["type"] == "datetime")
$body .= '
        $this->'.$field["name"].' = new \DateTime();
';
}
$body .= '
        return $this;
    }
';
$body .= 
'
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
';

foreach($fields as $field)
{
if(!is_array($field["type"])){
$type = $field["type"];
switch($type){
    case "integer": $type = "int";break;
    case "text": $type = "string";break;
    case "datetime": $type = "\DateTime";break;
}
$body .= 
'
    public function get'.ucfirst($field["name"]).'(): ?'.$type.'
    {
        return $this->'.$field["name"].';
    }

    public function set'.ucfirst($field["name"]).'(?'.$type.' $'.$field["name"].')
    {
        $this->'.$field["name"].' = $'.$field["name"].';
        return $this;
    }

';
}else{
$type = $field["type"][1];
$body .= 
'
    public function get'.ucfirst($field["name"]).'(): '.$type.'
    {
        return $this->'.$field["name"].';
    }

    public function set'.ucfirst($field["name"]).'('.$type.' $'.$field["name"].')
    {
        $this->'.$field["name"].' = $'.$field["name"].';
        return $this;
    }

';
}
}
    return $body;
    }

    private static function getEntityFileEnd(){
        return '}';
    }


    private static function getRepositoryFileContent($entityName)
    {
//-------------------
$content = 
'<?php

namespace App\Repository;

use App\\Entity\\'.$entityName.';
use VPFramework\Doctrine\Repository;

/**
 * @method '.$entityName.'|null find($id, $lockMode = null, $lockVersion = null)
 * @method '.$entityName.'|null findOneBy(array $criteria, array $orderBy = null)
 * @method '.$entityName.'[]    findAll()
 * @method '.$entityName.'[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class '.$entityName.'Repository extends Repository
{

}
';
//--------------------
        return $content;
    }

}