<?php
namespace W5n;

class Exception extends \Exception 
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
