<?php
namespace w5n;

use W5n\Exception;
use W5n\Model;

class DefaultModel extends Model
{
    protected $defaultFieldSearchNamespace = 'W5n\\Field\\';
    protected $fieldSearchNamespaces       = array();
    
    protected function getFullFieldClassNames($class)
    {
        $fullNames = array();
        foreach ($this->fieldSearchNamespaces as $namespace) {
            $fullNames[] = $namespace . $class;
        }
        $fullNames[] = $this->defaultFieldSearchNamespace . $class;
        return $fullNames;
    }
    
    public function addFilterSearchNamespace($namespace)
    {
        if (!substr($namespace, -1) == '\\')
            $namespace .= '\\';
        
        array_unshift($this->filterSearchNamespaces, $namespace);
    }
    
    public function removeFilterSearchNamespace($namespace)
    {
        if (!substr($namespace, -1) == '\\')
            $namespace .= '\\';
        
        $key = array_search($namespace, $this->filterSearchNamespaces);
        if ($key !== false)
            unset($this->filterSearchNamespaces[$key]);
    }
    
    public function __call($name, $arguments)
    {
        $className = ucfirst($name);
        $fullNames = $this->getFullFieldClassNames($className);
        
        foreach ($fullNames as $className) {
            if (class_exists($className)) {
                 try {
                    $class    = new \ReflectionClass($className);
                    $instance = $class->newInstanceArgs($arguments);
                    $this->addField($instance);
                    return $instance;
                } catch (\ReflectionException $ex) {
                    throw new Exception($ex->getMessage());
                }
            }
        }
        throw new Exception(sprintf('Field class "%s" not found.', $name));
    }    
}

