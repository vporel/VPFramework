<?php
namespace VPFramework\Utils;

use ReflectionProperty;
use Throwable;

trait FlexibleClassTrait
{
    public function __get($property)
    {
        return ObjectReflection::getPropertyValue($this, $property);
    }
}