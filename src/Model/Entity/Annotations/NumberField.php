<?php
namespace VPFramework\Model\Entity\Annotations;

/**
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
