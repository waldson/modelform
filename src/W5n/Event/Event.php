<?php 
namespace W5n\Event;

class Event
{

    protected $name;
    protected $timestamp;
    protected $params   = array();
    protected $canceled = false;
    protected $data     = null;

    public function __construct($name, array $params = array())
    {
        $this->name      = $name;
        $this->params    = $params;
        $this->timestamp = time();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($param, $default = null)
    {
        if (!isset($this->params[$param]))
            return $default;

        return $this->params[$param];
    }

    public function stopPropagation()
    {
        $this->canceled = true;
    }

    public function isCanceled()
    {
        return $this->canceled;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function getData()
    {
        return $this->data;
    }

}
