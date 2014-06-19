<?php
namespace W5n\Filter;

use W5n\Filter\AbstractFilter;

class Password extends AbstractFilter
{
    
    protected $hashFunction;
    
    public function __construct($hashFunction = 'md5')
    {
        $this->hashFunction = $hashFunction;
    }
    
    
    public function filter($value)
    {
        if (strlen($value) == '')
            return '';
        
        return call_user_func($this->hashFunction, $value);
    }

}

