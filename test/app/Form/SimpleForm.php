<?php
    
namespace App\Form;

use VPFramework\Form\Form;
use VPFramework\Form\Field\TextLine;
use VPFramework\Form\Field\Email;
use VPFramework\Form\Field\Password;
use VPFramework\Form\Field\Select;
use VPFramework\Form\Field\Number;
class SimpleForm extends Form{

    public function build(){
        $this
            ->addField(new TextLine("Field 1", "field1", $options = []))
            ->addField(new TextLine("Field 2", "field2", $options = []))
            ->addField(new Email("Field 3", "field3", $options = []))
            ->addField(new Password("Field 4", "field4", $options = []))
            ->addField(new Number("Field 6", "field6", $options = []));
    }
}
