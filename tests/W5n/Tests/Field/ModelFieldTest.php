<?php
namespace W5n\Tests\Field;

use W5n\Field\ModelField;
use W5n\Validation\AbstractValidator;
use W5n\Filter\AbstractFilter;

class ModelFieldTest extends \PHPUnit_Framework_TestCase
{
    private $field;
    private $model;
    private $validation;
    private $validation2;
    private $filter;
    private $filter2;
    private $fieldName  = 'testField';
    private $fieldLabel = 'testField';
    
    protected function setUp()
    {
        $this->field = new ModelField($this->fieldName, $this->fieldLabel);
        $this->model = $this->getMock('W5n\\Model');
        $this->validation     = $this->getMock(
            'W5n\\Validation\\AbstractValidator', 
            array('validate', 'getErrorMessage')
        );
        $this->validation2     = $this->getMock(
            'W5n\\Validation\\AbstractValidator', 
            array('validate', 'getErrorMessage')
        );
        $this->filter     = $this->getMock(
            'W5n\\Filter\\AbstractFilter', 
            array('filter')
        );
        $this->filter2     = $this->getMock(
            'W5n\\Filter\\AbstractFilter', 
            array('filter')
        );
    }
    
    public function testGetSetName()
    {
        $name = 'name';
        $newName = 'newName';
        $this->field->setName($name);
        $this->assertEquals($name, $this->field->getName());
        $this->field->name($newName);
        $this->assertEquals($newName, $this->field->getName());   
        $this->assertTrue($this->field->hasName());
        $this->field->clearName();
        $this->assertFalse($this->field->hasName());
    }
    
    public function testGetSetLabel()
    {
        $label    = 'label';
        $newLabel = 'newLabel';
        $this->field->setLabel($label);
        $this->assertEquals($label, $this->field->getLabel());
        $this->field->label($newLabel);
        $this->assertEquals($newLabel, $this->field->getLabel());     
        $this->assertTrue($this->field->hasLabel());
        $this->field->clearLabel();
        $this->assertFalse($this->field->hasLabel());
    }
    
    public function testGetSetInfo()
    {
        $info    = 'info';
        $newInfo = 'newInfo';
        $this->field->setInfo($info);
        $this->assertEquals($info, $this->field->getInfo());
        $this->field->info($newInfo);
        $this->assertEquals($newInfo, $this->field->getInfo());
        $this->assertTrue($this->field->hasInfo());
        $this->field->clearInfo();
        $this->assertFalse($this->field->hasInfo());
    }
    
    public function testHasModelParam()
    {
        $eventName = '##testEvent##';
        $field     = $this->field;
        $this->field->on($eventName, function($e) use ($field) {
            $params = $e->getParams();
            $this->assertCount(1, $params);
            $this->assertArrayHasKey('field', $params);
            $this->assertEquals($params['field'], $field);
        });        
        $this->field->trigger($eventName);
    }
    
    function testGetSetValue()
    {
        $value    = 'value';
        $newValue = 'newValue';
        
        $this->field->setValue($value);
        $this->assertEquals($value, $this->field->getValue());
        $return = $this->field->value($newValue);
        $this->assertEquals($return, $this->field);
        $this->assertEquals($newValue, $this->field->getValue());
    }
    
    
    function testGetSetError()
    {
        $error    = 'error';
        $newError = 'newError';
        
        $this->field->setError($error);
        $this->assertEquals($error, $this->field->getError());
        $return = $this->field->error($newError);
        $this->assertEquals($return, $this->field);
        $this->assertEquals($newError, $this->field->getError());
    }
    
    public function testGetSetModel()
    {
        $this->assertNull($this->field->getModel());
        $this->field->setModel($this->model);
        var_dump($this->field->getModel());
        $this->assertInstanceOf('W5n\\Model', $this->field->getModel());
        $this->assertEquals($this->model, $this->field->getModel());
        $this->assertEquals($this->model, $this->field->model());
    }
    
    public function testRemoveValidation()
    {
        $this->validation->expects($this->once())
            ->method('validate')->will($this->returnValue(true));
        $this->validation2->expects($this->exactly(2))
            ->method('validate')->will($this->returnValue(true));
        
        
        $this->assertEmpty($this->field->getValidations());
        $this->field->validation($this->validation); 
        $this->assertCount(1, $this->field->getValidations());
        $this->field->validation($this->validation2);
        $this->assertCount(2, $this->field->getValidations());
        $this->field->validate();
        $this->field->removeValidation($this->validation);
        $this->assertCount(1, $this->field->getValidations());
        $this->field->validate();
        $this->field->removeValidation($this->validation2);
        $this->assertCount(0, $this->field->getValidations());
        $this->field->validate();
    }
    
    public function testValidations()
    {
        $this->field->validations(
            array(
                array('validation' => $this->validation, 'errorMessage' => 'message'),
                $this->validation,
                $this->validation2
            )
        );
        $this->assertCount(2, $this->field->getValidations());
    }
    
    public function testValidation()
    {
        $this->validation->expects($this->exactly(2))
            ->method('validate')->will($this->onConsecutiveCalls(true, false));
        
        $this->validation->expects($this->once())
            ->method('getErrorMessage')->will(
                $this->returnValue('"$name$" is not valid. $value$ $rawValue$ $label$ $info$.')
            );
        
        $this->assertEmpty($this->field->getValidations());
        
        
        $data = array(
            'value'    => 'value',
            'rawValue' => 'value',
            'name'     => $this->fieldName,
            'label'    => $this->fieldLabel,
            'info'     => 'info'
        );
        
        $this->field->value($data['value']);
        $this->field->label($data['label']);
        $this->field->info($data['info']);
        
        
        $this->field->addValidation($this->validation);
        $this->assertCount(1, $this->field->getValidations());
        $this->assertTrue($this->field->validate());
        $this->assertFalse($this->field->validate());
        $error = $this->field->getError();
        $expectedErrorMessage = sprintf(
            '"%s" is not valid. %s %s %s %s.',
            $data['name'],
            $data['value'],
            $data['rawValue'],
            $data['label'],
            $data['info']
        );
        $this->assertEquals($expectedErrorMessage, $error);
        
        $this->field->clearValidations();
        $this->assertEmpty($this->field->getValidations());
    }
    
    public function testFilter()
    {
        $this->filter->expects($this->once())
            ->method('filter')->will($this->returnCallback('md5'));
        $this->filter2->expects($this->once())
            ->method('filter')->will($this->returnCallback('sha1'));
        
        $this->field->filter($this->filter);
        $this->field->filter($this->filter2);
        $value = 'value';
        $expectedValue = sha1(md5($value));
        $this->field->value($value);
        
        $this->assertEquals($expectedValue, $this->field->getValue());
        $this->assertNotEmpty($this->field->getFilters());
        $this->field->clearFilters();
        $this->assertEquals($value, $this->field->getValue());
        $this->assertEmpty($this->field->getFilters());
    }
    
    public function testFilters()
    {
        $this->assertEmpty($this->field->getFilters());
        $this->field->filters(array(
            $this->filter, 
            $this->filter2
        ));
        $this->assertCount(2, $this->field->getFilters());
    }
    
    public function testRemoveFilter()
    {
        $this->filter->expects($this->once())
            ->method('filter')->will($this->returnCallback('md5'));
        
        $this->field->filter($this->filter);
        $value = 'value';
        $expectedValue = md5($value);
        $this->field->value($value);        
        $this->assertEquals($expectedValue, $this->field->getValue());
        $this->field->removeFilter($this->filter);
        $this->assertEquals($value, $this->field->getValue());
    }
    
    public function testValidationWithCustomErrorMessage()
    {
        $this->validation->expects($this->exactly(2))
            ->method('validate')->will($this->onConsecutiveCalls(true, false));
        
        $data = array(
            'value'    => 'value',
            'rawValue' => 'value',
            'name'     => $this->fieldName,
            'label'    => $this->fieldLabel,
            'info'     => 'info'
        );
        
        $this->field->value($data['value']);
        $this->field->label($data['label']);
        $this->field->info($data['info']);
        
        
        $this->field->addValidation($this->validation, '"$name$" is not valid. $value$ $rawValue$ $label$ $info$.');
        $this->assertTrue($this->field->validate());
        $this->assertTrue($this->field->isValid());
        $this->assertFalse($this->field->hasError());
        $this->assertFalse($this->field->validate());
        $this->assertTrue($this->field->hasError());
        $this->assertFalse($this->field->isValid());
        $error = $this->field->getError();
        
        
        $this->field->clearError();
        $this->assertTrue($this->field->isValid());
        
        $expectedErrorMessage = sprintf(
            '"%s" is not valid. %s %s %s %s.',
            $data['name'],
            $data['value'],
            $data['rawValue'],
            $data['label'],
            $data['info']
        );
        
        $this->assertEquals($expectedErrorMessage, $error);
    }
    
    public function testValidationWithCallbackErrorMessage()
    {
        $this->validation->expects($this->exactly(2))
            ->method('validate')->will($this->onConsecutiveCalls(true, false));
        
        $data = array(
            'value'    => 'value',
            'rawValue' => 'value',
            'name'     => $this->fieldName,
            'label'    => $this->fieldLabel,
            'info'     => 'info'
        );
        
        $this->field->value($data['value']);
        $this->field->label($data['label']);
        $this->field->info($data['info']);
        
        
        $this->field->addValidation($this->validation, 
            function() {
                return '"$name$" is not valid. $value$ $rawValue$ $label$ $info$.';
            }
        );
        $this->assertTrue($this->field->validate());
        $this->assertFalse($this->field->validate());
        $error = $this->field->getError();
        $expectedErrorMessage = sprintf(
            '"%s" is not valid. %s %s %s %s.',
            $data['name'],
            $data['value'],
            $data['rawValue'],
            $data['label'],
            $data['info']
        );
        $this->assertEquals($expectedErrorMessage, $error);
    }
    
    /**
     * @dataProvider getSetOptionsProvider
     */
    public function testGetSetOption($option, $value)
    {
        $this->field->setOption($option, $value);
        $this->assertEquals($value, $this->field->getOption($option));
    }
    
    public function testGetSetOptions()
    {
        $options = array(
            'option1' => 'value1',
            'option2' => 'value2',
            'option3' => 'value3'
        );
        $this->assertCount(0, $this->field->getOptions());
        $this->field->setOptions($options);
        $this->assertCount(3, $this->field->getOptions());
        $this->assertEquals($options, $this->field->getOptions());
    }
    
    public function getSetOptionsProvider()
    {
        return array(
            array('option1', 'value1'),
            array('option2', 'value2'),
            array('option3', 'value3'),
            array('option4', 'value4'),
            array('option5', 'value5'),
            array('option6', 'value6'),
        );
    }
    
    
}

