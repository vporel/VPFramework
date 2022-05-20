<?php
namespace VPFramework\Form\Annotations;

/**
 * This annotation should be used on entities properties
 * @Annotation
 * @Target({"PROPERTY"})
 */
class NumberField extends Field
{

    /**
     * @var int
     */
    public $min = 0;

    /**
     * @var int
     */
    public $max = 999999999;

}
