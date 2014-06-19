<?php
namespace W5n\Filter;

use W5n\Filter\AbstractFilter;

class Boolean extends AbstractFilter
{
    
    public function filter($value)
    {
        if ($value == '0' || $value == 'false' || $value == 'off')
            $value = false;
        return ((bool)$value) ? '1' : '0';
    }

}

