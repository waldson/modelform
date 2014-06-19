<?php
namespace W5n\Filter;

interface Filter
{
    public function getOption($option, $default = null);
    public function setOption($option, $value);
    public function getOptions();
    public function setOptions(array $options);
    public function filter($value);    
}
