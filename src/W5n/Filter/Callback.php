<?php
namespace W5n\Filter;

use W5n\Filter\AbstractFilter;
use W5n\Filter\Exception;

class Callback extends AbstractFilter
{
    private $callback;
    
    public function __construct($callback)
    {
        if (!is_callable($callback, false, $callable_name)) {
            throw new Exception(sprintf('"%s is not callable.', $callable_name));
        }
        
        $this->callback = $callback;
    }
    
    public function filter($value)
    {
        return call_user_func($this->callback, $value);
    }

}

