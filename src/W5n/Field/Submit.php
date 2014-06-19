<?php
namespace W5n\Field;

use W5n\Field\DefaultModelField;

class Submit extends DefaultModelField
{
    public function __construct($value)
    {
        parent::__construct(null, null);
        $this->value($value);
        $this->setOption('type', 'submit');
    }
    
    protected function getRenderAttrs()
    {
        $attrs = parent::getRenderAttrs();
        $attrs['type'] = 'submit';
        return $attrs;
    }
    
}

