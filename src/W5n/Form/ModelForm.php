<?php 
namespace W5n\Form;

use W5n\Model;
use W5n\Event\EventDispatcher;
use W5n\Renderable;
use W5n\Form\Renderer\AbstractFormRenderer;
use W5n\Form\Renderer\DefaultFormRenderer;
use W5n\Field\DefaultModelField;
use W5n\Field\Submit;

class ModelForm extends EventDispatcher implements Renderable
{
    
    const METHOD_DELETE = 'delete';
    const METHOD_GET    = 'get';
    const METHOD_POST   = 'post';
    const METHOD_PUT    = 'put';

    const ENCTYPE_MULTIPART  = 'multipart/form-data';
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    
    protected $model;
    protected $attrs        = array();
    protected $renderer     = null;
    protected $extraFields  = array();
    protected $submitButtonKey = 'submit';

    public function __construct(Model $model, AbstractFormRenderer $renderer = null)
    {
        $this->model = $model;
        $this->setMethod(self::METHOD_POST);
        $this->setEnctype(self::ENCTYPE_URLENCODED);
        
        if ($renderer == null) {
            $renderer = new DefaultFormRenderer();
        }
        $this->setRenderer($renderer);
        
        $this->addExtraField($this->submitButtonKey, new Submit('Send'));
    }
    
    public function setAttr($attr, $value)
    {
        if ($value === false) {
            unset($this->attrs[$attr]);
            return;
        }
        
        $this->attrs[$attr] = $value;
        return $this;
    }
    
    public function getAttr($attr)
    {
        if (isset($this->attrs[$attr]))
            return $this->attrs[$attr];
    }
    
    public function getAttrs()
    {
        return $this->attrs;
    }
    
    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;
    }
    
    public function setEnctype($enctype)
    {
        return $this->setAttr('enctype', $enctype);
    }
    
    public function setAction($action)
    {
        return $this->setAttr('actio', $method);
    }
    
    public function setMethod($method)
    {
        return $this->setAttr('method', $method);
    }
    
    public function setId($id)
    {
        return $this->setAttr('id', $id);
    }
    
    public function setName($name)
    {
        return $this->setAttr('name', $name);
    }

    public function render()
    {
        return $this->renderer->render($this);
    }
    
    public function __toString()
    {
        return $this->render();
    }
    
    public function setLayout(array $layout)
    {
        return $this->renderer->setFormLayout($layout);
    }
    
    public function getLayout()
    {
        return $this->renderer->getFormLayout();
    }
    
    public function getFields()
    {
        $fields = $this->getModel()->getFields();
        return array_merge($this->extraFields, $fields);
    }
    
    public function validate()
    {
        return $this->getModel()->validate() && $this->validateExtraFields();
    }
    
    public function getValues()
    {
        $fields = $this->getFields();
        $values = array();
        foreach ($fields as $name => $f) {
            $values[$name] = $f->getValue();
        }
        return $values;
    }
    
    public function getValidValues()
    {
        $fields = $this->getFields();
        $values = array();
        foreach ($fields as $name => $f) {
            if ($f->isValid())
                $values[$name] = $f->getValue();
        }
        return $values;
    }
    
    public function populate(array $data)
    {
        $fields = $this->getFields();
        $values = array();
        foreach ($data as $name => $value) {
            if ($this->hasField($name))
                $fields[$name]->value($value);
        }
        return $this;
    }
    
    protected function validateExtraFields()
    {
        $valid = true;
        foreach ($this->extraFields as $f)
        {
            if (!$f->validate())
                $valid = false;
        }
        return $valid;
    }
    
    public function hasField($fieldName)
    {
        return $this->getModel()->hasField($fieldName) 
            || $this->hasExtraField($fieldName);
    }
    
    public function getField($fieldName)
    {
        $field = $this->getModel()->getField($fieldName);
        if ($field === null)
            return $this->getExtraField($fieldName);
        
        return $field;
    }
    
    public function addExtraField($name, DefaultModelField $field)
    {
        $this->extraFields[$name] = $field;
    }
    
    public function removeExtraField($name)
    {
        unset($this->extraFields[$name]);
    }
    
    public function getExtraField($name)
    {
        if ($this->hasExtraField($name))
            return $this->extraFields[$name];
    }
    
    public function hasExtraField($name)
    {
        return isset($this->extraFields[$name]);
    }
    
    public function getExtraFields()
    {
        return $this->extraFields;
    }
    
    public function getModel()
    {
        return $this->model;
    }

    public function setRenderer(AbstractFormRenderer $renderer)
    {
        $this->renderer = $renderer;
    }
    
    public function getRenderer()
    {
        return $this->renderer;
    }

}
