<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;
use DateTime;

class Date extends AbstractValidator 
{
    
    public function __construct($format = 'Y-m-d')
    {
        $this->setOption('format', $format);
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return '"$rawValue$" is not a valid date.';
    }

    public function validate($value)
    {
        if (strlen($value) == 0)
            return true;
        
        $date = DateTime::createFromFormat(
            $this->getOption('format', 'Y-m-d'), $value
        );
        return !empty($date);
    }

}
