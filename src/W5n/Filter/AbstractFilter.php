<?php
namespace W5n\Filter;

use W5n\Filter\Filter;

abstract class AbstractFilter implements Filter
{
    protected $options = array();

    public function getOption($option, $default = null)
    {
        if (!isset($this->options[$option]))
            return $default;
        
        return $this->options[$option];
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

}
