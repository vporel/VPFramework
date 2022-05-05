<?php
namespace VPFramework\Utils;


class ClassUtil
{  


    /**
     * @param string $class
     * 
     * @return string
     */
    public static function getSimpleName(string $class):string
    {
        $classNameParts = explode("\\", $class);
        return end($classNameParts);
    }

    /**
     * Retourne les noms des champs de l
     * @return array Tableau associant chaque propriété de l'attribut à son type dans doctrine (Ex : name => string)
     */
    public function getFields(){
        return Entity::getFields($this->entityClass);
    }

    /**
     * Vérifie si l'entité courant provient du framework
     */
    public function isBuiltin(){
        return in_array($this->getName(), ["Admin", "AdminGroup", "AdminGroupPermission"]);
    }


}