<?php
namespace W5n\Form\Renderer;

use W5n\Form\Renderer\DefaultFormRenderer;
use W5n\Helper\Html;
use W5n\Field\DefaultModelField;
use W5n\Form\ModelForm;

class Bootstrap3Renderer extends DefaultFormRenderer
{
    
    public function renderOpenForm(ModelForm $form)
    {
        $class = $form->getAttr('class');
        $form->setAttr('class', trim('form '.$class));
        return parent::renderOpenForm($form);
    }
    
    public function renderCloseRow(ModelForm $form)
    {
        return Html::tagClose('div');
    }

    public function renderContent($name, $content, ModelForm $form, $size = null)
    {
        $containerClasses = array('container-content');
        $hasSize = $this->hasSize($size);
        
        if ($hasSize)
            $containerClasses[] = 'col-sm-' . $size;
        
        if (!$hasSize)
            return $content;
        
        return Html::tag(
            'div',
            array(
                'id' => 'container-content-' . $name,
                'class' => implode(' ', $containerClasses)
            ),
            $content
        ); 
    }

    public function renderField(DefaultModelField $field, ModelForm $form, $size = null)
    {
        $classes = array();
        $hasSize = $this->hasSize($size);
        
        if ($hasSize)
            $classes[] = 'col-sm-' . $size;
        
        $type = $field->getOption('type', 'text');
        
        $name = $field->getName();
        $id   = 'field-' . $name;
        
        $field->once(
            DefaultModelField::EVENT_RENDER_SET_ATTRS, 
            function() use ($id) {
                return array('id' => $id);
            }
        );
        
        if ($type == 'text') {
            $field->once(
                DefaultModelField::EVENT_RENDER_SET_CLASSES, 
                function () use ($hasSize, $size) {
                    $classes = array('form-control');
                    if ($hasSize)
                        $classes[] = 'col-sm-' . $size;
                    
                    return implode(' ', $classes);
                }
            );
        }
        
        switch ($type) {
            case 'checkbox':
            case 'radio':
                $containerClasses = array($type);
                if ($hasSize)
                    $containerClasses[] = 'col-sm-' . $size;
                
                $content = Html::tag('br', array(), false)
                         . Html::label($field->render() . $field->getLabel(), $id);
                if ($field->hasInfo() || $field->hasError()) {
                    $content .= Html::tag(
                        'span', 
                        array('class' => 'help-block'),
                        $field->hasError() ? $field->getError() : $field->getInfo()
                    );
                }
                
                return Html::tag(
                    'div',
                    array(
                        'id' => 'container-' . $id,
                        'class' => implode(' ', $containerClasses)
                    ),
                    $content
                );
            case 'submit':
                $containerClasses[] = 'submit-container';
                if ($hasSize)
                    $containerClasses[] = 'col-sm-' . $size;
                $field->once(DefaultModelField::EVENT_RENDER_SET_CLASSES, function() {
                   return 'btn btn-default';
                });
                return Html::tag(
                    'div',
                    array(
                        'class' => implode(' ', $containerClasses)
                    ),
                    $field->render()
                ); 
 
                break;
            default:
                $containerClasses = array('form-group');
                if ($hasSize)
                    $containerClasses[] = 'col-sm-' . $size;
                
                if ($field->hasError())
                    $containerClasses[] = 'has-error';
                
                $content = Html::label(
                   $field->getLabel(), $id, array('class' => 'control-label')
                );
                
                $content .= $field->render();
                if ($field->hasInfo() || $field->hasError()) {
                    $content .= Html::tag(
                        'span', 
                        array('class' => 'help-block'),
                        $field->hasError() ? $field->getError() : $field->getInfo()
                    );
                }
                
               return Html::tag(
                    'div',
                    array(
                        'id' => 'container-' . $id,
                        'class' => implode(' ', $containerClasses)
                    ),
                    $content
                ); 
                
                
        }
    }
    
    protected function hasSize($size)
    {
        return !empty($size) && is_numeric($size) && $size > 0 && $size <= 12;
    }

    public function renderOpenRow(ModelForm $form)
    {
        return Html::tagOpen('div', array('class' => 'row'));
    }

}

