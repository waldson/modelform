<?php
namespace W5n\Form\Renderer;

use W5n\Form\Renderer\AbstractFormRenderer;
use W5n\Field\DefaultModelField;
use W5n\Form\ModelForm;
use W5n\Helper\Html;

class DefaultFormRenderer extends AbstractFormRenderer
{

    public function renderCloseForm(ModelForm $form)
    {
        return Html::tagClose('form');
    }

    public function renderCloseRow(ModelForm $form)
    {
        return Html::tagClose('div');
    }

    public function renderField(DefaultModelField $field, ModelForm $form, $size = null)
    {
        $template = '<div id="field-%s-container">%s%s</div>';
        
        return sprintf(
            $template, 
            $field->getName(), 
            Html::label($field->getLabel(), 'field-'.$field->getName()),
            $field->render()
        );
    }

    public function renderOpenForm(ModelForm $form)
    {
        return Html::tagOpen('form', $form->getAttrs());
    }

    public function renderOpenRow(ModelForm $form)
    {
        return Html::tagOpen('div');
    }

    public function renderContent($name, $content, ModelForm $form, $size = null)
    {
        return Html::tag('div', array('id' => 'content-'.$name), $content);
    }

    public function renderCloseFieldset($legend, ModelForm $form, $layout)
    {
        return Html::tagClose('fieldset');
    }

    public function renderOpenFieldset($legend, ModelForm $form, $layout)
    {
        return Html::tagOpen('fieldset') . Html::tag('legend', array(), $legend);
    }

}
    