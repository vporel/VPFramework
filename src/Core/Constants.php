<?php

namespace VPFramework\Core;

class Constants{

    const FRAMEWORK_ROOT = __DIR__."/..";


    
    /**/
    const PROJECT_ROOT = self::FRAMEWORK_ROOT."/../../VPFramework-test"; //In dev mode according to the test folder
    /**/
    /** 
    const PROJECT_ROOT = self::FRAMEWORK_ROOT."/../../../.."; //In production mode
    /**/

    const APP_DIR = self::PROJECT_ROOT."/App";
        const CONTROLLER_DIR = self::APP_DIR."/Controller";
    const PUBLIC_DIR = self::PROJECT_ROOT."/Public";
    const CONFIG_DIR = self::PROJECT_ROOT."/Config";
    const VIEW_DIR = self::PROJECT_ROOT."/View";
    const BIN_DIR = self::PROJECT_ROOT."/Bin";

    const CONTROLLER_NAMESPACE = "App\\Controller";
}
