<?php
namespace W5n\Event;

use W5n\Event\Event;
use W5n\Event\Exception;
use ReflectionClass;

class EventDispatcher 
{
    
    protected $events = array();
    
    static public function create()
    {
        $class = new ReflectionClass(get_called_class());
        return $class->newInstanceArgs(func_get_args());
    }
    
    public function on($eventName, $callback, $prepend = false)
    {
        return $this->_on($eventName, $callback, $prepend);
    }
    
    public function once($eventName, $callback, $prepend = false)
    {
        return $this->_on($eventName, $callback, $prepend, true);
    }
    
    public function off($eventName, $callback = null)
    {
        if (!isset($this->events[$eventName]) || $callback === null) {
            unset($this->events[$eventName]);
            return $this;
        } 
        
        foreach ($this->events[$eventName] as $k => $l) {
            if ($callback == $l['callback']) {
                unset($this->events[$eventName][$k]);
                break;
            }
        }
        
        return $this;
    }
    
    public function trigger($eventName, $params = array(), $startData = null)
    {
        if (!isset($this->events[$eventName]))
            return;
        
        $this->modifyEventParams($params);
        $event = new Event($eventName, $params);
        $calls = array();
        $lastCall = $startData;
        foreach ($this->events[$eventName] as $k => $e) {
            if ($event->isCanceled())
                break;
            
            $event->setData($lastCall);
            $callback = $e['callback'];
            $lastCall = call_user_func($callback, $event);
            
            if ($lastCall !== null)
                $calls[] = $lastCall;
            
            if ($e['once'])
                unset($this->events[$eventName][$k]);
        }
        
        return $calls;
    }
    
    protected function _on($eventName, $callback, $prepend = true, $once = false)
    {
        if (!isset($this->events[$eventName]))
            $this->events[$eventName] = array();
        
        if (!is_callable($callback, false, $callable_name)) {
            throw new Exception(sprintf('"%s is not callable.', $callable_name));
        }
        
        $listenerData = array(
            'callback' => $callback,
            'once'     => $once
        );
        
        if ($prepend)
            array_unshift($this->events[$eventName], $listenerData);
        else
            $this->events[$eventName][] = $listenerData;
        
        return $this;
    }
    
    protected function modifyEventParams(&$params) {}
}

