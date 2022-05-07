<?php

namespace VPFramework\Core;

class Constants{
    /*
     * Cette propriété particulière est initialisée mise à jour dans le fichier index.php(dossier public) d'ouverture du site
     * Si cette initialisation n'est pas faite, bon nombre de classes ne fonctionneront pas
     */
    public static $APP_ROOT;
    
    public static $PUBLIC_FOLDER;

    const FRAMEWORK_ROOT = __DIR__."/..";

    const CONTROLLER_NAMESPACE = "App\\Controller";
}
