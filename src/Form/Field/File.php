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
        return '<input type="file" name="'.$this->name.'" class="form-control" id="'.$this->name.'" accept=".'.implode(',.', $this->getExtensions()).'"/>';
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
                    $this->error = $e->getMessage();
                }
            }else{                
                $this->error = $e->getMessage();
            }
            return "";
        }
    }
}