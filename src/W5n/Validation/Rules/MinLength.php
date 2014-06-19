<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class MinLength extends AbstractValidator 
{
    
    public function __construct($min)
    {
        $this->setOption('minLength', $min);
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return sprintf(
            '%s must have more than %i characters.', 
            $params['name'], $this->getOption('minLength')
        );
    }

    public function validate($value)
    {
        return function_exists('mb_strlen') 
            ? mb_strlen($value) >= $this->getOption('minLength')
            : strlen($value) >= $this->getOption('minLength');
    }

}
