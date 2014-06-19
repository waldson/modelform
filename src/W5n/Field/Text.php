<?php
namespace W5n\Field;

use W5n\Field\DefaultModelField;

class Text extends DefaultModelField 
{
    
    public function __construct($name, $label = null, $required = false)
    {
        parent::__construct($name, $label);
        if ($required) {
            $this->required();
            $this->setOption('required', 1);
        }
        
        $this->filter('trim');
        $this->validations();
    }
    
}

