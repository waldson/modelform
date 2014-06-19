<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class MaxLength extends AbstractValidator 
{
    
    public function __construct($max)
    {
        $this->setOption('maxLength', $max);
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return sprintf(
            '%s must have less than %i characters.', 
            $params['name'], $this->getOption('maxLength')
        );
    }

    public function validate($value)
    {
        return function_exists('mb_strlen') 
            ? mb_strlen($value) <= $this->getOption('maxLength')
            : strlen($value) <= $this->getOption('maxLength');
    }

}
