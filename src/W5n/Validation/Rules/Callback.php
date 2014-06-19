<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;
use W5n\Validation\Rules\Exception;

class Callback extends AbstractValidator
{
    private $callback;
    
    public function __construct($callback)
    {
        if (!is_callable($callback, false, $callable_name)) {
            throw new Exception(sprintf('"%s" is not callable.', $callable_name));
        }
        
        $this->callback = $callback;
    }
    
    public function validate($value)
    {
        $value = call_user_func($this->callback, $value);
        return (bool) $value;
    }

    public function getErrorMessage($value, $params = null)
    {
        return '%rawValue% is not a valid value.';
    }
}