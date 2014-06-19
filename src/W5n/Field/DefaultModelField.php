<?php 
namespace W5n\Field;

use W5n\Exception;
use W5n\Renderable;
use W5n\Field\ModelField;
use W5n\Helper\Html;

class DefaultModelField extends ModelField implements Renderable
{
    const EVENT_RENDER_SET_CLASSES    = 'renderSetClasses';
    const EVENT_RENDER_SET_ATTRS      = 'renderSetAttrs';
    const EVENT_RENDER_SET_DECORATORS = 'renderSetDecorators';
    
    protected $callContext = 'validations';
    protected $defaultValidatorSearchNamespace = 'W5n\\Validation\\Rules\\';
    protected $defaultFilterSearchNamespace    = 'W5n\\Filter\\';
    protected $validatorSearchNamespaces       = array();
    protected $filterSearchNamespaces          = array();
    
    public function filters(array $filters = array())
    {
        $this->callContext = 'filters';
        return parent::filters($filters);
    }
    
    public function validations(array $validations = array())
    {
        $this->callContext = 'validations';
        return parent::validations($validations);
    }
    
    public function addValidatorSearchNamespace($namespace)
    {
        if (!substr($namespace, -1, 1) != '\\')
            $namespace .= '\\';
        
        array_unshift($this->validatorSearchNamespaces, $namespace);
    }
    
    public function removeValidatorSearchNamespace($namespace)
    {
        if (!substr($namespace, -1, 1) != '\\')
            $namespace .= '\\';
        
        $key = array_search($namespace, $this->validatorSearchNamespaces);
        if ($key !== false)
            unset($this->validatorSearchNamespaces[$key]);
    }
    
    public function clearValidatorSearchNamespaces()
    {
        $this->validatorSearchNamespaces = array();
    }
    
    public function getValidatorSearchNamespaces()
    {
        return $this->validatorSearchNamespaces;
    }
    
    public function addFilterSearchNamespace($namespace)
    {
        if (!substr($namespace, -1, 1) != '\\')
            $namespace .= '\\';
        
        array_unshift($this->filterSearchNamespaces, $namespace);
    }
    
    public function removeFilterSearchNamespace($namespace)
    {
        if (!substr($namespace, -1, 1) != '\\')
            $namespace .= '\\';
        
        $key = array_search($namespace, $this->filterSearchNamespaces);
        if ($key !== false)
            unset($this->filterSearchNamespaces[$key]);
    }
    
    public function clearFilterSearchNamespaces()
    {
        $this->filterSearchNamespaces = array();
    }
    
    public function getFilterSearchNamespaces()
    {
        return $this->filterSearchNamespaces;
    }
    
    protected function getFullClassNames($class, $type)
    {
        $ns  = $type == 'filters' ? $this->filterSearchNamespaces 
                                  : $this->validatorSearchNamespaces;
        
        $dns = $type == 'filters' ? $this->defaultFilterSearchNamespace 
                                  : $this->defaultValidatorSearchNamespace;
        
        $fullNames = array();
        foreach ($ns as $n) {
            $fullNames[] = $n . $class;
        }
        $fullNames[] = $dns . $class;
        return $fullNames;
    }
    
    public function __call($name, $arguments)
    {
        $className      = ucfirst($name);
        $fullClassNames = $this->getFullClassNames($className, $this->callContext);
        $method         = $this->callContext == 'filters' ? 'filter' : 'validation';
        foreach ($fullClassNames as $className) {
            if (class_exists($className)) {
                 try {
                    $class    = new \ReflectionClass($className);
                    $instance = $class->newInstanceArgs($arguments);
                    return $this->$method($instance);
                } catch (\ReflectionException $ex) {
                    throw new Exception($ex->getMessage());
                }
            }
        }
        throw new Exception(
            sprintf(
                '%s class "%s" not found.', 
                $this->callContext == 'filters' ? 'Filter' : 'Validation',
                ucfirst($name)
            )
        );
    }

    public function render()
    {
        $attrs = $this->getRenderAttrs();
        if (!is_array($attrs))
            $attrs = array();
        
        $tAttrs = $this->trigger(self::EVENT_RENDER_SET_ATTRS);
        if (!empty($tAttrs)) {
            foreach ($tAttrs as $attrGroup) {
                if (is_array($attrGroup))
                    $attrs = array_merge($attrs, $attrGroup);
            }
        }


        $classes = $this->trigger(self::EVENT_RENDER_SET_CLASSES);
        if (!empty($classes)) {
                $classes = implode(' ', $classes);

            if (!isset($attrs['class']))
                $attrs['class'] = $classes;
            else
                $attrs['class'] = trim($attrs['class']) . ' ' . $classes;
        }
        
        $input  = Html::inputText($this->getName(), $this->getValue(), $attrs);
        $inputs = $this->trigger(self::EVENT_RENDER_SET_DECORATORS, array(
            'field' => $this
        ), $input);
        
        if (!empty($inputs))
            $input = $inputs[count($inputs)-1];
        
        return $input;
    }
    
    protected function getRenderAttrs()
    {
        $attrs = array(
            'type' => 'text',
            'id'   => 'field-'.$this->getName()
        );
        
        //if ($this->getOption('required', false)) {
        //    $attrs['required'] = 'required';
        //}
        
        return $attrs;
    }
    
    public function __toString()
    {
        return $this->render();
    }

    

}
