<?php

namespace VPFramework\Core\Configuration;
/**
 * Recupération des services configurés par le développeur
 */
class SecurityConfiguration{
    private $rules = null;

    public function getRules(){
        if($this->rules === null){
            $this->rules = require ROOT."/config/security.php";
        }
        return $this->rules;
    }
}