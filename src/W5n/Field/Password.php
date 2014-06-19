<?php
namespace W5n\Field;

use W5n\Field\Text;

class Password extends Text
{
    
    public function __construct(
        $name, $label = null, $required = false, $hashFunction = 'md5'
    ) {
        parent::__construct($name, $label, $required);
        $this->filters()->password($hashFunction)->validations();
    }
    
    protected function getRenderAttrs()
    {
        $attrs = parent::getRenderAttrs();
        $attrs['class'] = 'field-password';
        $attrs['value'] = '';
        $attrs['type']  = 'password';
        
        return $attrs;
    }
    
}

