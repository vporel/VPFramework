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

}