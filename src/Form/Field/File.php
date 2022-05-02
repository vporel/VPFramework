<?php

namespace VPFramework\Form\Field;

use VPFramework\Utils\FileUpload;
use VPFramework\Utils\FileUploadException;
use VPFramework\Utils\ObjectReflection;

class File extends AbstractField
{
    public function __construct($label, $name, $options = [])
    {
        $this
            ->addOption("extensions", [])
            ->addOption("folder", "");
            
        parent::__construct($label, $name, $options);       
    }

    protected function getCustomHTMLForFilter(): string{}

    public function getCustomHTML($value){
        if($this->isReadOnly())
            return '
                <span class="file-current-value">'.$value.'</span>
            ';
        else
            return '
                <input type="file" name="'.$this->name.'" class="form-control" id="'.$this->name.'" '.((count($this->getExtensions())>0) ?  'accept=".'.implode(',.', $this->getExtensions()).'"' : "").'/>
                <span class="file-current-value">'.$value.'</span>
            ';
    }

    public function getRealValue($value){
        return $this->getFileBaseName();
    }

    /**
     * Retourne le nom final du fichier après son téléchargement
     */
    public function getFileBaseName()
    {
        try{
            $fileBaseName = FileUpload::upload($this->name, $this->getFolder(), $this->getExtensions()); 
            return $fileBaseName;
        }catch(FileUploadException $e){
            if($e->getCode() == FileUploadException::FILE_NOT_RECEIVED){
                if(!$this->isNullable()){
                    $this->error = "Choisissez un fichier";
                }
            }else{                
                $this->error = $e->getMessage();
            }
            return "";
        }
    }

    public function isValid($value):bool{
        try{
            FileUpload::testValidity($this->name, $this->getExtensions());
            return true;
        }catch(FileUploadException $e){
            if($e->getCode() == FileUploadException::FILE_NOT_RECEIVED){
                if(!$this->isNullable()){
                    $this->error = "Choisissez un fichier";
                }
            }else{                
                $this->error = $e->getMessage();
            }
            return false;
        }
    }
}