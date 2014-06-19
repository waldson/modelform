<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class RequiredIf extends AbstractValidator 
{
    private $condition;
    
    public function __construct($condition)
    {
        $this->condition = $condition;
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return null;
    }

    public function validate($value)
    {
        $condition = $this->condition;

        if (is_bool($condition)) {
            if (!$condition)
                return true;
        } else {
            if (is_callable($condition))
            {
                if ($condition instanceof \Closure)
                    $condition = $condition($value, $this->getOptions ());
                else
                    $condition = call_user_func($value, $this->getOptions());
                
                if (!(bool)$condition)
                    return true;
            }
        }
        
        return is_string($value) ? strlen(trim($value)) > 0 : !empty($value);
    }

}
