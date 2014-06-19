<?php
namespace W5n\Field;

use W5n\Field\DefaultModelField;

class Boolean extends DefaultModelField
{
    
    public function __construct($name, $label = null)
    {
        parent::__construct($name, $label);
        $this->filters()->filter('trim')->boolean()->validations();
        $this->setOption('type', 'checkbox');
    }
    
    protected function getRenderAttrs()
    {
        $attrs = parent::getRenderAttrs();
        $attrs['type']  = 'checkbox';
        $attrs['value'] = '1';
        if ($this->getValue())
            $attrs['checked'] = 'checked';
        
        return $attrs;
    }
    
}

