<?php
namespace VPFramework\Model\Entity\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class TextLineField extends Field
{
    /**
     * Motif que la chaine doit respecter
     * 
     * @var string
     */
    public $pattern = "#^(.[\s]*.*)*$#";

    public $patternMessage = "Le motif n'est pas respecté";

    /**
     * @var int
     */
    public $minLength = 0;

    /**
     * @var int
     */
    public $maxLength = 9999999;

}
