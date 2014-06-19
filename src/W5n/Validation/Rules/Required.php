<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class Required extends AbstractValidator 
{
    
    public function getErrorMessage($value, $params = null)
    {
        return '"$name$" is required.';
    }

    public function validate($value)
    {
        return is_string($value) ? strlen(trim($value)) > 0 : !empty($value);
    }

}
