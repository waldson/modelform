<?php
namespace W5n;

use W5n\Event\EventDispatcher;
use W5n\Field\ModelField;

class Model extends EventDispatcher 
{
    
    protected $fields  = array();
    protected $options = array();
    protected $isValid = true;
    protected $errors  = array();
    
    public function field(ModelField $field)
    {
        $this->addField($field);
        return $this;
    }
    
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }
    
    public function getField($fieldName)
    {
        if (!$this->hasField($fieldName))
            return null;
        
        return $this->fields[$fieldName];
    }
    
    public function addField(ModelField $field)
    {
        $name = $field->getName();
        $field->setModel($this);
        $this->fields[$name] = $field;
    }
    
    public function removeField($fieldName)
    {
        unset($this->fields[$fieldName]);
    }
    
    protected function modifyEventParams(&$params)
    {
        $params['model'] = $this;
    }
    
    public function getOption($option, $default = null)
    {
        if (!isset($this->options[$option]))
            return $default;

        return $this->options[$option];
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;        
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }
    
    public function validate()
    {
        $this->setValid(true);
        $this->errors = array();
        
        $fields = $this->getFields();        
        foreach ($fields as $name => $field) {
            if (!$field->validate()) {
                $this->setValid(false);
                $this->errors[$name] = $field->getError();
            }
        }
        
        return $this->isValid();
    }
    
    public function populate(array $data)
    {
        foreach ($data as $f => $v) {
            $field = $this->getField($f);
            if ($field !== null)
                $field->value($v);
        }
        return $this;
    }
    
    public function getValues($filterCallback = null)
    {
        $values = array();
        $fields = $this->getFields();        
        foreach ($fields as $name => $field) {
            if (!empty($filterCallback)) {
                $result = call_user_func($filterCallback, $field);
                if ($result === false)
                    continue;
            }
            $values[$name] = $field->getValue();
        }
        
        return $values;
    }
    
    public function getValidValues()
    {
        return $this->getValues(function ($field){
            return $field->isValid();
        });
    }
    
    public function isValid()
    {
        return $this->isValid;
    }
    
    public function setValid($isValid)
    {
        $this->isValid = $isValid;
    }

    public function getFields()
    {
      return $this->fields;  
    }
    
    public function getErrors() 
    {
        return $this->errors;
    }
    
    public function __set($name, $value)
    {
        if (isset($this->fields[$name]))
            $this->getField($name)->value($value);
    }
    
    public function __get($name)
    {
        if (isset($this->fields[$name]))
            return $this->getField($name)->getValue();
    }

}
