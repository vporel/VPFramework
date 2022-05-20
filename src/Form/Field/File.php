<?php

namespace VPFramework\Form\Field;

use VPFramework\Utils\FileUpload;
use VPFramework\Utils\FileUploadException;
use VPFramework\Utils\ObjectReflection;

class File extends AbstractField
{
    /**
     * @var array
     */
    private $extensions = [];

    /**
     * @var string
     */
    private $folder = "";

    public function __construct(string $label, string $name, array $extensions, string $folder)
    {
            
        parent::__construct($label, $name);       
        $this->extensions = $extensions;
        $this->folder = $folder;
    }

   

    protected function getCustomHTMLForFilter(): string{}

    /**
     * @param string $lastUpdatedfileBaseName Valeur du champ hidden $name-last-updated-file
     * 
     */
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
    
    public function getRealValue($value)
    {
        return $this->getFileBaseName();
    }

    /**
     * 
     * @return string|null
     */
    public function getFileBaseName():?string
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
                }else{
                    return true;
                }
            }else{                
                $this->error = $e->getMessage();
            }
            return false;
        }
    }

    /**
     * Get the value of extensions
     *
     * @return  array
     */ 
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Set the value of extensions
     *
     * @param  array  $extensions
     *
     * @return  self
     */ 
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Get the value of folder
     *
     * @return  string
     */ 
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set the value of folder
     *
     * @param  string  $folder
     *
     * @return  self
     */ 
    public function setFolder(string $folder)
    {
        $this->folder = $folder;

        return $this;
    }
}