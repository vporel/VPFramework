<?php

namespace VPFramework\Form\Field;

use DateTime;

abstract class AbstractField implements \Serializable
{
    protected $label;
    protected $name;
    protected $options = [
            'default' => '',
            'ignored' => false, // Cette option est mise à "true" sur un champ n'étant pas un attribut de la classe entité à la quelle le formulaire est lié
            'nullable' => true, 
            'readOnly' => false
        ];
    private $validationRules = [];
    protected $error = '';

    /**
     * Constructeur.
     *
     * @param $label
     * @param $name
     * @param $options
     */
    public function __construct($label, $name, $options = [])
    {
        $this->label = $label;
        $this->name = $name;
        $this->options = array_slice(array_merge($this->options, $options), 0, count($this->options));
        $this->addValidationRule('Ce champ doit être renseigné', function($value){
            if(is_array($value)){
                return $this->isNullable() || count($this->getRealValue($value)) > 0;
            }
            return $this->isNullable() || trim($value) != '';
        });
    }

    protected function addOption(string $name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param callable $rule
     */
    public function addValidationRule(string $message, callable $rule)
    {
        $this->validationRules[] = new ValidationRule($message, $rule);
        return $this;
    }

    public function existOption(string $name)
    {
        return array_key_exists($name, $this->options);
    }

    public function __call($name, $arguments)
    {
        $nameStart2 = substr($name, 0, 2);
        $nameStart3 = substr($name, 0, 3);
        if ($nameStart2 == 'is') {
            $option = lcfirst(substr($name, 2, strlen($name)));
            if (array_key_exists($option, $this->options)) {
                return $this->options[$option];
            } elseif (array_key_exists($name, $this->options)) {
                return $this->options[$name];
            } else {
                throw new \Exception("L'option $option n'existe pas");
            }
        } elseif ($nameStart3 == 'get' || $nameStart3 == 'set') {
            $option = lcfirst(substr($name, 3, strlen($name)));
            if (array_key_exists($option, $this->options)) {
                if ($nameStart3 == 'get') {
                    return $this->options[$option];
                } elseif ($nameStart3 == 'set') {
                    $this->options[$option] = $arguments[0];

                    return $this;
                }
            } else {
                throw new \Exception("L'option $option n'existe pas");
            }
        }
        throw new \Exception("Fonction $name n'existe pas");
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasError()
    {
        return $this->error != "";
    }
    public function getError()
    {
        return $this->error;
    }

    public function getRealValue($value)
    {
        return $value;
    }
    
    /**
     * Code HTML du champ sans le cadre et le label
     * Ex : return <input ...>
     */
    abstract protected function getCustomHTML($value);

    public function getHTML($value)
    {
        if($value instanceof DateTime){
            $value = $value->format("Y-m-d");
        }

        return '
            <div class="form-group">
                <label class="form-label" for="'.$this->getName().'">'.$this->getLabel().'</label>
                <div class="input-div">
                    '.$this->getCustomHTML($value).' 
                    <span class="form-field-error text-error">'.$this->error.'</span>
                </div>
            </div>
        ';
    }

    abstract protected function getCustomHTMLForFilter():string;

    public function getHTMLForFilter(){
        $calledClassArray = explode("\\", get_called_class());
        return '
            <div class="filter" data-field-class="'.end($calledClassArray).'" data-field-name="'.$this->getName().'">
                <label>'.$this->getLabel().'</label>
                '.$this->getCustomHTMLForFilter().' 
            </div>
        ';
    }

    public function isValid($value)
    {
        foreach($this->validationRules as $rule){
            if(!$rule->getRule()($value)){
                $this->error = $rule->getMessage();
                return false;
            }
        }
        return true;
        
    }

    protected function getReadOnlyText(){
        return $this->isReadOnly() ? "readonly" : "";
    }

    /**
     * Retourne un tableau avec les informations importantes pour le champ
     * Cette fonction peut être appelée si par exemple, un script javascript doit utliser ces informations
     * @return array
     */
    public function serialize()
    {
        $calledClassArray = explode("\\", get_called_class());

        return [
            'label' => $this->getLabel(),
            'name' => $this->getName(),
            "default" => $this->getDefault(),
            'required' => $this->isRequired(),
            'readOnly' => $this->isReadOnly(),
            "class"=> end($calledClassArray)
        ];
    }

    public function unserialize($data)
    {
    }
}
