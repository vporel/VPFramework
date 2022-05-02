<?php

namespace VPFramework\Form\Field;

use DateTime;

class Date extends AbstractInput
{
    protected function getInputType()
    {
        return 'date';
    }

    protected function getCustomHTMLForFilter(): string
    {
        
    }
    public function getRealValue($value)
    {
        if($value == "")
            return new DateTime();
        return new DateTime($value);
    }
}
