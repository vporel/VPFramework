<?php
namespace VPFramework\View;



/**
 * Les extensions personnalisées doivent hériter de cette classe
 * Elles seront alors listées dans le fichier Config\app.php
 * Ex : 
 *      "view_extensions" => [
 *         "App\ViewExtensions\MyViewExtension"
 *      ]
 */
abstract class ViewExtension
{
    public abstract function getGlobals():array;

    public abstract function getFunctions():array;
}