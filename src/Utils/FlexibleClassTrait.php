<?php
namespace VPFramework\Utils;

trait FlexibleClassTrait
{
    public function __get($property)
    {
        $method = "get".ucfirst($property);
        return $this->$method();
    }
}