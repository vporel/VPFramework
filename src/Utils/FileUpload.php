<?php
namespace VPFramework\Utils;

use VPFramework\Core\Constants;

class FileUpload
{
    /**
     * Chiffre à ajouter à la fin du nom du fichier s'il existe déjà  
     */
    private static $newFilePathCounter = 2;

    /**
     * @param string $key
     * @param string $destinationFolder A partir du dossier public
     * @param array $extensions Tableau des extensions acceptées (en minuscule)
     * 
     * @return string "" si l'upload échoue. Sinon, le basename(fileName+extension) du fichier après le téléversement
     */
    public static function upload(string $key, $destinationFolder, $extensions = [])
    {
        if(isset($_FILES[$key]["name"]) && $_FILES[$key]["name"] != ""){ 
            $fileName = $_FILES[$key]["name"];
            $pathinfo = pathinfo($fileName);
            $extension = strtolower($pathinfo["extension"]);
            if(count($extensions) == 0 || in_array($extension, $extensions)){
                $absoluteDestFolder = self::absoluteDestinationFolder($destinationFolder);
                $filePath = $absoluteDestFolder."/".$fileName;
                self::$newFilePathCounter = 2;
                $newFilePath = self::newFilePath($filePath);
                if(!file_exists($absoluteDestFolder))
                    mkdir($absoluteDestFolder, 0777, true);
                if(move_uploaded_file($_FILES[$key]["tmp_name"], $newFilePath)){
                    return pathinfo($newFilePath)["basename"];
                }
            }else{
                throw new FileUploadException(FileUploadException::WRONG_EXTENSION, $extension);
            }
        }else{
            throw new FileUploadException(FileUploadException::FILE_NOT_RECEIVED, $key);
        }
        return "";
    }

    /**
     * Exécute un suite de tests
     * Si une exception ets lancée, le fichier n'est pas envoyé ou est invalide
     */
    public static function testValidity(string $key, $extensions = []):void
    {
        if(isset($_FILES[$key]["name"]) && $_FILES[$key]["name"] != ""){ 
            $fileName = $_FILES[$key]["name"];
            $pathinfo = pathinfo($fileName);
            $extension = strtolower($pathinfo["extension"]);
            if(count($extensions) != 0 && !in_array($extension, $extensions)){
               throw new FileUploadException(FileUploadException::WRONG_EXTENSION, $extension);
            }
        }else{
            throw new FileUploadException(FileUploadException::FILE_NOT_RECEIVED, $key);
        }
    }

    private static function absoluteDestinationFolder($destinationFolder){
        if($destinationFolder != null && $destinationFolder != "")
            return Constants::$PUBLIC_FOLDER."/".$destinationFolder;
        else
            return Constants::$PUBLIC_FOLDER;
    }

    /**
     * Function appelée pour le renommage du fichier si un autre du même nom existe déjà
     */
    private static function newFilePath($filePath){
        if(file_exists($filePath)){
            //On réessaye avec en incrémentant le compteur
            $pathinfo = pathinfo($filePath);
            $updatedFilePath = $pathinfo["dirname"]."/".$pathinfo["filename"]."-".self::$newFilePathCounter.".".$pathinfo["extension"];
            self::$newFilePathCounter++;
            return self::newFilePath($updatedFilePath);
        }else{
            return $filePath;
        }
    }
}