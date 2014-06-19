<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;

class Range extends AbstractValidator
{
    
    public function __construct($min, $max)
    {
        $this->setOption('min', $min);
        $this->setOption('max', $max);
    }
    
    public function validate($value)
    {
        if (!is_numeric($value))
            return false;
        
        $min = $this->getOption('min', -INF);
        $max = $this->getOption('max', INF);
        
        return $value >= $min && $value <= $max;
    }

    public function getErrorMessage($value, $params = null)
    {
        return '%rawValue% is not in range.';
    }
}