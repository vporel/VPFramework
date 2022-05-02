<?php

namespace VPFramework\Form\Field;

class Email extends AbstractInput
{
    protected function getCustomHTMLForFilter(): string
    {
        return "<input type='text'/>";
    }
    protected function getInputType(){ return "email"; }
}