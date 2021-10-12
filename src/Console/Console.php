<?php

namespace VPFramework\Console;

class Console
{
    public static function input($message, $default = ""){
        echo "\n".$message." ";
        $var = "";
        fscanf(STDIN, "%s", $var);
        $var = trim($var);
        if($var == "")
            return $default;
        return $var;
    }
}