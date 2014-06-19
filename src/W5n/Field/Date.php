<?php
namespace W5n\Field;

use W5n\Field\Text;
use DateTime;

class Date extends Text
{
    
    public function __construct(
        $name, $label = null, $required = false, 
        $inputFormat = 'd/m/Y', $outputFormat = 'Y-m-d'
    ) {
        parent::__construct($name, $label, $required);
        $this->filters()->date($inputFormat, $outputFormat)
            ->validations()->date($outputFormat);
        
        $this->setOption('inputFormat', $inputFormat);
        $this->setOption('outputFormat', $outputFormat);
    }
    
    protected function getRenderAttrs()
    {
        $attrs = parent::getRenderAttrs();
        $attrs['class'] = 'input-date';
        
        $value       = trim($this->getRawValue());        
        $inputFormat = $this->getOption('inputFormat', 'Y-m-d');
        $outputFormat = $this->getOption('outputFormat', 'Y-m-d');
        
        $date = DateTime::createFromFormat($outputFormat, $value);
        if ($date)
            $attrs['value'] = $date->format($inputFormat);
        else
            $attrs['value'] = $value;
        
        return $attrs;
    }
    
}

