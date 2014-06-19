<?php 
namespace W5n\Validation;

interface Validator
{
    public function setOption($option, $value);
    public function getOption($option, $default = null);
    public function getOptions();
    public function setOptions(array $options);
    public function getErrorMessage($value, $params = null);    
    public function validate($value);
}
