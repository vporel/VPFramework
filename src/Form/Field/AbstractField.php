<?php

namespace VPFramework\Form\Field;

use DateTime;

abstract class AbstractField implements \Serializable
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var mixed
     */
    protected $defaultValue = null;
    /**
     * @var bool
     */
    protected $ignored = false; // Cette option est mise à "true" sur un champ n'étant pas un attribut de la classe entité à la quelle le formulaire est lié
    /**
     * @var bool
     */
    protected $nullable = true;

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * @var array
     */
    private $validationRules = [];

    /**
     * @var string
     */
    protected $error = '';

    /**
     * Constructeur.
     *
     * @param $label
     * @param $name
     */
    public function __construct(string $label, string $name)
    {
        $this->label = $label;
        $this->name = $name;
        $this->addValidationRule("nullability", 'Ce champ doit être renseigné', function($value){
            if(is_array($value)){
                return $this->isNullable() || count($this->getRealValue($value)) > 0;
            }
            return $this->isNullable() || trim($value) != '';
        });
    }

    /**
     * @param callable $rule
     */
    public function addValidationRule(string $ruleName, string $message, callable $rule)
    {
        $this->validationRules[$ruleName] = new ValidationRule($ruleName, $message, $rule);
        return $this;
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

    public function getHTML($value, $attrs = [])
    {
        if($value instanceof DateTime){
            $value = $value->format("Y-m-d");
        }
        $classAttrText = "form-group";
        $attrsText = "";
        foreach($attrs as $name => $value){
            if(strtolower($name) != "class"){
                $attrsText .= ' '.$name.'="'.$value.'"';
            }else{
                $classAttrText .= " ".$attrs[$name];
            }
        }
        return '
            <div class="'.$classAttrText.'" '.$attrsText.'>
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

    /**
     * @param mixed $value
     * 
     * @return bool
     */
    public function isValid($value):bool
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
            'label' => $this->label,
            'name' => $this->name,
            "default" => $this->defaultValue,
            'nullable' => $this->nullable,
            'readOnly' => $this->readOnly,
            "class"=> end($calledClassArray)
        ];
    }

    public function unserialize($data)
    {
    }

    /**
     * Get the value of defaultValue
     *
     * @return  mixed
     */ 
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set the value of defaultValue
     *
     * @param  mixed  $defaultValue
     *
     * @return  self
     */ 
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get the value of ignored
     *
     * @return  bool
     */ 
    public function isIgnored()
    {
        return $this->ignored;
    }

    /**
     * Set the value of ignored
     *
     * @param  bool  $ignored
     *
     * @return  self
     */ 
    public function setIgnored(bool $ignored)
    {
        $this->ignored = $ignored;

        return $this;
    }

    /**
     * Get the value of nullable
     *
     * @return  bool
     */ 
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * Set the value of nullable
     *
     * @param  bool  $nullable
     *
     * @return  self
     */ 
    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Get the value of readOnly
     *
     * @return  bool
     */ 
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * Set the value of readOnly
     *
     * @param  bool  $readOnly
     *
     * @return  self
     */ 
    public function setReadOnly(bool $readOnly)
    {
        $this->readOnly = $readOnly;

        return $this;
    }
}
