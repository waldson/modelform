<?php
namespace W5n\Validation\Rules;

use W5n\Validation\AbstractValidator;
use DateTime;

class FutureDate extends AbstractValidator 
{
    
    public function __construct($includesToday = false, $format = 'Y-m-d')
    {
        $this->setOption('format', $format);
        $this->setOption('includesToday', $includesToday);
    }
    
    public function getErrorMessage($value, $params = null)
    {
        return '"$rawValue$" is not a future date.';
    }

    public function validate($value)
    {
        if (strlen($value) == 0)
            return true;
        
        $format        = $this->getOption('format', 'Y-m-d');
        $includesToday = $this->getOption('includesToday', false);
        
        $date = DateTime::createFromFormat($format, $value);
        $date->setTime(0, 0, 0);
        
        if (empty($date))
            return false;
        
        $todayDate = new DateTime('today');
        
        return $includesToday 
            ? $date->getTimestamp() >= $todayDate->getTimestamp()
            : $date->getTimestamp() > $todayDate->getTimestamp();
    }

}
