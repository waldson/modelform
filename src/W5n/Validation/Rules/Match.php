<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;
use W5n\Field\ModelField;

class Match extends AbstractValidator 
{
    protected $field;
    
    public function __construct(ModelField $field)
    {
        $this->field = $field;
    }
    
    
    public function getErrorMessage($value, $params = null)
    {
        return sprintf('"$name$" doesn\'t match "%s"', $this->field->getName());
    }

    public function validate($value)
    {
        return $value == $this->field->getValue();
    }

}
