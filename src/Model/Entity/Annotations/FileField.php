<?php
namespace VPFramework\Model\Entity\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class FileField extends Field
{
    /**
     * Dossier de stockage des fichiers
     * On part du dossier Public
     * Si aucun dossier n'est précisé, le dossier Public est le dossier par défaut
     * 
     * @var string
     */
    public $folder = "";

    /**
     * Les extensions acceptées, un tableau vide signifie que toutes les extensions sont acceptées
     * 
     * @var array
     */
    public $extensions = [];

}
