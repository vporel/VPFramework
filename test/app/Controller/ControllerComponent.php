<?php
    
namespace App\Controller;

use App\Form\SimpleForm;
use VPFramework\Core\Controller;
use VPFramework\Core\Request;

class ControllerComponent extends Controller
{    
    public function simpleForm(Request $request)
    {
        $form = new SimpleForm(NULL, NULL);
        $form->setParameters($request->getAll());
        return $this->render("components/simpleForm.html.php", compact("form"));
    }

    public function multipleForm()
    {

    }
}
