<?php
namespace VPFramework\Form\Annotations;

/**
 * This annotation should be used on entities properties
 * @Annotation
 * @Target({"PROPERTY"})
 */
class PasswordField extends TextLineField
{
    /**
     * Fonction de hachage du mot de passe
     * 
     * @var string
     */
    public $hashFunction = "sha1";

}
