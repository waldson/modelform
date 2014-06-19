<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class Equals extends AbstractValidator 
{
    private $value;
    
    public function __construct($other)
    {
        $this->value = $other;
    }
    
    public function getErrorMessage($value, $params = null)
    {
        var_dump($value, $this->value);
        return sprintf('"%s" is not equal to "%s".', $value, $this->value);
    }

    public function validate($value)
    {
        return $value == $this->value;
    }

}
