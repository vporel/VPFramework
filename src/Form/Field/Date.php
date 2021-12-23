<?php

namespace VPFramework\Form\Field;

use DateTime;

class Date extends AbstractInput
{
    protected function getType()
    {
        return 'date';
    }
    public function getRealValue($value)
    {
        if($value == "")
            return new DateTime();
        return new DateTime($value);
    }
}
