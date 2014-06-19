<?php
namespace W5n\Field;

use W5n\Field\Text;
use W5n\Helper\Html;

class Email extends Text
{
    
    public function __construct($name, $label = null, $required = false)
    {
        parent::__construct($name, $label, $required);
        $this->email();
    }
    
    protected function getRenderAttrs()
    {
        $attrs = parent::getRenderAttrs();
        $attrs['class'] = 'input-email';
        $attrs['type']  = 'email';
        
        return $attrs;
    }
}

