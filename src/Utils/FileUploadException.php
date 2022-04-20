<?php
namespace VPFramework\Utils;

class FileUploadException extends \Exception
{
    const FILE_NOT_RECEIVED = 0;
    const WRONG_EXTENSION = 1;

    public function __construct($code, $element = "")
    {
        
        if($element == "")
            $msg = "Code : $code";
        else{
            switch($code){
                case self::FILE_NOT_RECEIVED: $msg = "Clé $element non trouvée dans la varible \$_FILES";break;
                case self::WRONG_EXTENSION: $msg = "L'extension $element n'est pas acceptée";break;
            }
        }
        parent::__construct($msg, $code);
    }
}