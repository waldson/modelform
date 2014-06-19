<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class InArray extends AbstractValidator 
{
    private $values;
    
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return sprintf('"%s" is not a valid value.', $value);
    }

    public function validate($value)
    {
        return in_array($value, $this->values);
    }

}
