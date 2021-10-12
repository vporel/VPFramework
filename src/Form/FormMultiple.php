<?php

namespace VPFramework\Form;

use Doctrine\ORM\EntityManager;
use VPFramework\Core\DIC;
use VPFramework\Form\Field\Field;

abstract class FormMultiple extends Form
{

    private $objects;
    private $entityClass;

    public function __construct($entityClass, $repositoryClass, $objects = null){
        $getCalledClass = explode("\\", get_called_class());
        $this->name = end($getCalledClass);
        $this->objects = $objects;
        $this->entityClass = $entityClass;
        $this->repositoryClass = $repositoryClass;
        $this->repository = DIC::getInstance()->get($repositoryClass);

        $this->build();

    }

    public function getEntityClass(){ return $this->entityClass; }

    public function save(EntityManager $entityManager)
    {
        if($this->objects == null){
            for($i = 1;$i<= (int) $this->parameters['nb-lines']; $i++){
                $object = new $this->entityClass();
                $filesField = [];
                foreach($this->fields as $field){
                    if($field->getClass() != "File"){
                        $method = "set".ucfirst($field->getName());
                        $value = $field->getRealValue($this->parameters[$field->getName()."-".$i]);
                        if(method_exists($object, $method)){
                            $object->$method($value);
                        }
                    }else{
                        $filesField[] = $field;
                    }
                }
                $entityManager->persist($object);
                $entityManager->flush();
                // Uploading files
                foreach($filesField as $field){
                    $field->uploadFile($object->getId(), $field->getName()."-".$i, $object);
                }
            }
        }
    }

    public function createHTML()
    {
        $html = '
            <div class="form-error on-top">'.$this->error.'</div>
            <input type="hidden" name="form-'.$this->name.'"/>
            <input type="hidden" value="0" class="nb-lines"name="nb-lines"/>
	    ';
        $html .= '
            <table>
		        <thead>
			        <tr>
				        <th>N°</th>
        ';
		foreach($this->fields as $field){
            if(in_array($field->getClass(), ["Select", "Relation"])){ 
                $html .= '
                            <th>'.$field->getLabel().'</th>
                            <input type="hidden" class="field-model" data-elements=\''.$field->getElementsJSON().'\' data-label="'.$field->getLabel().'"data-name="'.$field->getName().'" data-class="'.$field->getClass().'" data-pattern="'.$field->getPattern().'" data-default="'.$field->getDefault().'"/>
                ';
            }else{
                $html .= '
                            <th>'.$field->getLabel().'</th>
                            <input type="hidden" class="field-model" data-label="'.$field->getLabel().'"data-name="'.$field->getName().'" data-class="'.$field->getClass().'" data-pattern="'.$field->getPattern().'" data-default="'.$field->getDefault().'"/>
                ';
            }
        }
        $html .= '
				        <th></th>
			        </tr>
		        </thead>
		        <tbody data-nb-lines="0" class="lines"></tbody>
		        <tfoot>
                    <tr>
                        <td colspan="2">
                            Ajouter lignes : 
                            <input type="number"class="lines-to-add"value="1"/>
                            <button class="add-lines btn btn-action"style="height:35px;"><i class="fas fa-plus"></i></button>
                        </td>
                        <td colspan="2"style="text-align:right">
                            <button class="reset btn btn-bad">
                                <i class="fas fa-times"></i>
                                <em>Annuler</em>
                            </button>
                            <button class="save btn btn-primary">
                                <i class="fas fa-save"></i>
                                <em>Enregistrer</em>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        ';
        return $html;

    }

}