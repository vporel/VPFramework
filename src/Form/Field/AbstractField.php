<?php

namespace VPFramework\Form\Field;

abstract class AbstractField implements \Serializable
{
    protected $label;
    protected $name;
    protected $options = [
            'default' => '',
            'required' => false,
            'isIgnored' => false, // Cette option est mise à "true" sur un champ n'étant pas un attribut de la classe entité à la quelle le formulaire est lié
        ];
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
    }

    protected function addOption(string $name, $value)
    {
        $this->options[$name] = $value;

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

    public function getError()
    {
        return $this->error;
    }

    public function getRealValue($value)
    {
        return $value;
    }

    abstract public function getFieldHTML();

    public function createHTML()
    {
        /*
            Cette function est faite pour être redéfinie par la class Password uniquement
         */
        return '
            <div class="form-group">
                <label class="form-label" for="'.$this->name.'">'.$this->label.'</label>
                '.$this->getFieldHTML().'
                <span class="form-field-error text-error">'.$this->error.'</span>
            </div>
        ';
    }

    public function isValid($value)
    {
        if ($this->isRequired()) {
            if (trim($value) != '') {
                return true;
            } else {
                $this->error = 'Ce champ doit être renseigné';

                return false;
            }
        } else {
            return true;
        }
    }

    public function serialize()
    {
        $calledClassArray = explode("\\", get_called_class());

        return [
            'label' => $this->getLabel(),
            'name' => $this->getName(),
            "default" => $this->getDefault(),
            'required' => $this->isRequired(),
            "class"=> end($calledClassArray)
        ];
    }

    public function unserialize($data)
    {
    }
}
