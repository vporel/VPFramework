<?php
namespace VPFramework\Model\Entity\Annotations;

/**
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
