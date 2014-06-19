<?php 
namespace W5n\Field;

use W5n\Event\EventDispatcher;
use W5n\Validation\Validator;
use W5n\Model;
use W5n\Filter\Filter;
use W5n\Filter\Callback as CallbackFilter;
use W5n\Validation\Rules\Callback as CallbackValidation;

class ModelField extends EventDispatcher
{
    
    protected $name;
    protected $value;
    protected $label;
    protected $info;
    protected $model;
    protected $error;
    protected $isValid     = true;
    protected $validations = array();
    protected $filters     = array();    
    protected $options     = array();

    public function __construct($name, $label = null)
    {
        $this->name  = $name;
        $this->label = $label;
    }
    
    public function name($name) 
    {
        $this->setName($name);
        return $this;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName() 
    {
        return $this->name;
    }
    
    public function hasName()
    {
        return strlen((string)$this->name) > 0;
    }
    
    public function clearName()
    {
        return $this->name = null;
    }
    
    public function label($label)
    {
        $this->setLabel($label);
        return $this;
    }
    
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    public function getLabel() 
    {
        return $this->label;
    }
    
    public function hasLabel()
    {
        return strlen((string)$this->label) > 0;
    }
    
    public function clearLabel()
    {
        return $this->label = null;
    }
    
    public function info($info) 
    {
        $this->setInfo($info);
        return $this;
    }
    
    public function setInfo($info)
    {
        $this->info = $info;
    }
    
    public function getInfo() 
    {
        return $this->info;
    }
    
    public function hasInfo()
    {
        return strlen((string)$this->info) > 0;
    }
    
    public function clearInfo()
    {
        $this->info = null;
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
    
    public function error($error)
    {
        $this->setError($error);
        return $this;
    }
    
    public function setError($error) 
    {
        $this->error = $error;
    }
    
    public function getError() 
    {
        return $this->error;
    }
    
    public function hasError()
    {
        return strlen((string)$this->error) > 0;
    }
    
    public function clearError()
    {
        $this->error  = null;
        $this->setValid(true);
    }
    
    public function setModel(Model $model)
    {
        $this->model = $model;
    }
    
    public function getModel() 
    {
        return $this->model;
    }
    
    public function model()
    {
        return $this->model;
    }

    public function value($value)
    {
        $this->setValue($value);
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        $value = $this->getRawValue();
        
        if (empty($this->filters))
            return $value;
        
        foreach ($this->filters as $f)
        {
            $value = $f->filter($value);
        }
        return $value;
    }
    
    public function getRawValue()
    {
        return $this->value;
    }
    
    public function filter($filter)
    {
        if (!($filter instanceof Filter))
            $filter = new CallbackFilter($filter);
        
        $filter->setOption('field', $this);
        $this->addFilter($filter);
        return $this;
    }
    
    public function addFilter(Filter $filter)
    {
        $this->filters[spl_object_hash($filter)] = $filter;
    }
    
    public function removeFilter(Filter $filter)
    {
        unset($this->filters[spl_object_hash($filter)]);
    }
    
    public function filters(array $filters = array())
    {
        foreach ($filters as $f)
        {
            $this->filter($f);
        }
        return $this;
    }
    
    public function clearFilters()
    {
        $this->filters = array();
    }
    
    public function getFilters()
    {
        return $this->filters;
    }
    
    public function validation($validator, $errorMessage = null)
    {
        if (!($validator instanceof Validator)) 
            $validator = new CallbackValidation($validator);
        
        $validator->setOption('field', $this);
        
        $this->addValidation($validator, $errorMessage);
        return $this;
    }
    
    public function addValidation(Validator $validator, $errorMessage = null)
    {
        $this->validations[spl_object_hash($validator)] = array(
            'validation'   => $validator,
            'errorMessage' => $errorMessage
        );
    }
    
    public function removeValidation(Validator $validator)
    {
        unset($this->validations[spl_object_hash($validator)]);
    }
    
    public function validations(array $validations = array())
    {
        foreach ($validations as $v)
        {
            if (is_array($v) && isset($v['validation']))
                $this->validation($v['validation'], isset($v['errorMessage']) ? $v['errorMessage'] : null);
            else
                $this->validation($v);
        }
        return $this;
    }
    
    public function clearValidations()
    {
        $this->validations = array();
    }
    
    
    public function getValidations()
    {
        return $this->validations;
    }
    
    public function setValid($valid)
    {
        $this->valid = (bool)$valid;
    }
    
    public function isValid()
    {
        return $this->valid;
    }
    
    public function validate()
    {
        $this->clearError();
        
        if (empty($this->validations))
            return true;
        
        $value = $this->getValue();
        
        foreach ($this->validations as $v) {
            $validation = $v['validation'];
            if (!$validation->validate($value)) {
                $errorMessage = null;
                
                $errorMessageParams = array(
                    'field' => $this
                );
                
                if (isset($v['errorMessage'])) {
                    $errorMessage = $v['errorMessage'];
                } else {
                    $errorMessage = $validation->getErrorMessage(
                        $value, 
                        $errorMessageParams
                    );
                }
                
                if (is_callable($errorMessage)) {
                    $errorMessage = call_user_func(
                        $errorMessage, 
                        $value, 
                        $errorMessageParams
                    );
                }
                
                $params = array(
                    '$value$'    => $value,
                    '$rawValue$' => $this->getRawValue(),
                    '$name$'     => $this->getName(),
                    '$label$'    => $this->getLabel(),
                    '$info$'     => $this->getInfo()
                );
                
                $errorMessage = strtr($errorMessage, $params);
                $this->setError($errorMessage);
                $this->setValid(false);
                return false;
            }
        }
        
        return true;
    }
    
    protected function modifyEventParams(&$params)
    {
        $params['field'] = $this;
    }
    
}