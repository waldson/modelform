<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class Email extends AbstractValidator 
{
    
    public function getErrorMessage($value, $params = null)
    {
        return '"$value$" is not a valid email address.';
    }

    public function validate($value)
    {
        if (strlen($value) == 0)
            return true;
        
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}
