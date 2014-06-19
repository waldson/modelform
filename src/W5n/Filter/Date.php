<?php
namespace W5n\Filter;

use W5n\Filter\AbstractFilter;
use DateTime;

class Date extends AbstractFilter
{
    protected $inputFormat;
    protected $outputFormat;
    
    public function __construct($inputFormat = 'd/m/Y', $outputFormat = 'Y-m-d')
    {
        $this->inputFormat  = $inputFormat;
        $this->outputFormat = $outputFormat;
    }
    
    
    public function filter($value)
    {
        $inputDate = DateTime::createFromFormat($this->inputFormat, $value);
        if (empty($inputDate))
            return $value;

        return $inputDate->format($this->outputFormat);
    }

}

