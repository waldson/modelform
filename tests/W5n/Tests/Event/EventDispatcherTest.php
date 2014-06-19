<?php
namespace W5n\Tests;

use W5n\Event\EventDispatcher;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;
    private $object;
    
    protected function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->object     = $this->getMock(
            'stdClass', array('callbackMethod', 'callbackMethod2')
        );
    }
    
    public function testCreate()
    {
        $this->assertInstanceOf(
            '\\W5n\\Event\\EventDispatcher', EventDispatcher::create()
        );
    }
        
    public function testTrigger()
    {
        $this->object->expects($this->any())
             ->method('callbackMethod')
             ->will($this->returnValue('foo'));
        
        $event  = '##event##';
        $event2 = '##event2##';
        
        
        $this->dispatcher->on($event, array($this->object, 'callbackMethod'));
        $this->dispatcher->on($event, array($this->object, 'callbackMethod'));
        $this->dispatcher->on($event2, array($this->object, 'callbackMethod'));

        
        $result = $this->dispatcher->trigger($event);
        $expectedResult = array('foo', 'foo');        
        $this->assertEquals($expectedResult, $result);
        
        $this->dispatcher->trigger($event);
        $this->dispatcher->trigger($event2);
    }
    
    public function testOn()
    {
        $times = 3;
        
        $this->object->expects($this->exactly($times))
             ->method('callbackMethod')
             ->will($this->returnValue('foo'));
       
        $event      = '##event##';
        $otherEvent = '##event2##';
        
        $this->dispatcher->on($event, array($this->object, 'callbackMethod'));
        $this->dispatcher->on($otherEvent, array($this->object, 'callbackMethod'));
        
        for ($i = 0; $i < $times; $i++) {
            $this->dispatcher->trigger($event);
        }        
    }
    
    public function testOnce()
    {
        $times = 3;
        
        $this->object->expects($this->once())
             ->method('callbackMethod')
             ->will($this->returnValue('foo'));
       
        $event      = '##event##';
        
        $this->dispatcher->once($event, array($this->object, 'callbackMethod'));
        
        for ($i = 0; $i < $times; $i++) {
            $this->dispatcher->trigger($event);
        }
    }
    
    public function testOff()
    {
        $this->object->expects($this->never())->method('callbackMethod');
        $this->object->expects($this->once())->method('callbackMethod2');
        $event = '##event##';
        
        $this->dispatcher->on($event, array($this->object, 'callbackMethod'));
        $this->dispatcher->on($event, array($this->object, 'callbackMethod2'));
        
        $this->dispatcher->off($event, array($this->object, 'callbackMethod'));
        
        $this->dispatcher->trigger($event);
        
        $this->dispatcher->off($event);
        
        $this->dispatcher->trigger($event);
    }
    
    public function testEventCanceled()
    {
        $this->object->expects($this->never())->method('callbackMethod2');
        $event = '##event##';
        
        $this->dispatcher->on($event,  function ($e) use ($event) {
            $e->stopPropagation();
            $this->assertEquals($event, $e->getName());
        });
        $this->dispatcher->on($event, array($this->object, 'callbackMethod2'));
        for ($i = 0; $i < 5; $i++) {
            $this->dispatcher->trigger($event);
        }
    }
    
    public function testCallbackNotCallable()
    {
        $this->setExpectedException('W5n\\Event\\Exception');
        $this->dispatcher->on('#notdefinemethod', '#notdefinemethod');
    }

}

