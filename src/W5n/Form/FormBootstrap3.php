<?php 
namespace W5n\Form;

use W5n\Form\ModelForm;
use W5n\Model;
use W5n\Form\Renderer\Bootstrap3Renderer;

class FormBootstrap3 extends ModelForm
{
    public function __construct(Model $model)
    {
        parent::__construct($model, new Bootstrap3Renderer());
    }
}
