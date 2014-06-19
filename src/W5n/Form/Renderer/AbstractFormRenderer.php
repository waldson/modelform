<?php
namespace W5n\Form\Renderer;

use W5n\Field\DefaultModelField;
use W5n\Form\ModelForm;
use W5n\Exception;

abstract class AbstractFormRenderer
{
    protected $formLayout = array();

    abstract public function renderField(DefaultModelField $field, ModelForm $form, $size = null);
    abstract public function renderContent($name, $content, ModelForm $form, $size = null);
    abstract public function renderOpenFieldset($legend, ModelForm $form, $layout);
    abstract public function renderCloseFieldset($legend, ModelForm $form, $layout);
    abstract public function renderOpenForm(ModelForm $form);
    abstract public function renderCloseForm(ModelForm $form);
    abstract public function renderOpenRow(ModelForm $form);
    abstract public function renderCloseRow(ModelForm $form);
    
    public function setFormLayout(array $layout)
    {
        $this->formLayout = $layout;
    }
    
    public function getFormLayout() 
    {
        return $this->formLayout;
    }
    
    public function render(ModelForm $form)
    {
        $layout = $this->getFormLayout();
        
        $formStr = $this->renderOpenForm($form);
        $fields  = $form->getFields();
        
        if (empty($layout)) {
            foreach ($fields as $name => $field) {
                $formStr .= $this->renderOpenRow($form);
                $formStr .= $this->renderField($field, $form);
                $formStr .= $this->renderCloseRow($form);
            }
        } else {
            foreach ($layout as $idx => $row) {
                $formStr .= $this->renderRow($form, $row);
            }
        }
        
        
        $formStr .= $this->renderCloseForm($form);
        return $formStr;
    }
    
    protected function renderFieldset(ModelForm $form, array $fieldset)
    {
        $legend   = $fieldset[0];
        $fields   = $fieldset[1];
        
        $fieldsetStr = $this->renderOpenFieldset($legend, $form, $fields);
        foreach ($fields as $row)
        {
            if ($this->isFieldsetRow($row))
                $fieldsetStr .= $this->renderFieldset($form, $row);
            else
                $fieldsetStr .= $this->renderRow($form, $row);
        }
        $fieldsetStr .= $this->renderCloseFieldset($legend, $form, $fields);
        
        return $fieldsetStr;
    }    
    
    protected function renderRow(ModelForm $form, array $row)
    {
        $rowStr = '';
        
        if ($this->isFieldsetRow($row))
            $rowStr .= $this->renderFieldset($form, $row);
        else {
            $rowStr .= $this->renderOpenRow($form);
            foreach ($row as $key => $value) {
                if ($this->isContent($key, $value)) {
                    $rowStr .= $this->renderContent($key, $value[0], $form, $value[1]);
                } else {
                    if (!$form->hasField($key))
                        throw new Exception(sprintf('Field "%s" not found in form object', $key));
                    
                    $rowStr .= $this->renderField($form->getField($key), $form, $value);
                }
            }
            $rowStr .= $this->renderCloseRow($form);
        }
        return $rowStr;
    }
    
    protected function isFieldsetRow($row)
    {
        if (!is_array($row))
            return false;
        
        $keys = array_keys($row);
        
        return count($keys) == 2 
            && is_numeric($keys[0]) 
            && is_numeric($keys[1])
            && is_string($row[$keys[0]]) 
            && is_array($row[$keys[1]]);
    }
    
    protected function isField($key, $value)
    {
        return is_string($key) && is_numeric($value);
    }
    
    protected function isContent($key, $value)
    {
        return is_string($key) && is_array($value);
    }
        
}
