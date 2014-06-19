<?php
namespace W5n\Tests;

use W5n\Model;
use W5n\Field\ModelField;
use W5n\Validation\Rules\Required;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    
    private $model;
    private $field;
    private $field2;
    private $fieldName  = 'testField';
    private $fieldName2 = 'testField2';
    private $required;
    
    protected function setUp()
    {
        $this->model    = new Model();
        $this->field    = new ModelField($this->fieldName);
        $this->field2   = new ModelField($this->fieldName2);
        $this->required = $this->getMock(
            'W5n\\Validation\\AbstractValidator', 
            array('validate', 'getErrorMessage')
        );
    }
    
    public function testAddField()
    {
        $this->assertCount(0, $this->model->getFields());
        $this->model->addField($this->field);
        $fields = $this->model->getFields();
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey($this->fieldName, $fields);
        $this->assertEquals($this->field, $fields[$this->fieldName]);
        $this->assertEquals($this->field, $this->model->getField($this->fieldName));
    }
    
    public function testRemoveField()
    {
        $this->assertFalse($this->model->hasField($this->fieldName));
        $this->assertFalse($this->model->hasField($this->fieldName2));
        
        $this->model->addField($this->field);        
        $this->model->field($this->field2);     
        
        $this->assertTrue($this->model->hasField($this->fieldName));
        $this->assertTrue($this->model->hasField($this->fieldName2));
        
        
        $this->model->removeField($this->fieldName);
        
        $this->assertFalse($this->model->hasField($this->fieldName));
        $this->assertTrue($this->model->hasField($this->fieldName2));
        
        $this->model->removeField($this->fieldName2);
        
        $this->assertFalse($this->model->hasField($this->fieldName));
        $this->assertFalse($this->model->hasField($this->fieldName2));
    }
    
    public function testHasModelParam()
    {
        $eventName = '##testEvent##';
        $model     = $this->model;
        $this->model->on($eventName, function($e) use ($model) {
            $params = $e->getParams();
            $this->assertCount(1, $params);
            $this->assertArrayHasKey('model', $params);
            $this->assertEquals($params['model'], $model);
        });        
        $this->model->trigger($eventName);
    }
    
    public function testMagicGetSet()
    {
        $fieldName = $this->fieldName;
        
        $this->assertNull($this->model->$fieldName);
        $this->model->$fieldName = 'value';
        $this->assertNull($this->model->$fieldName);
        
        $this->model->addField($this->field);
        
        $this->model->$fieldName = 'value';
        $this->assertEquals($this->model->$fieldName, $this->field->getValue());
        $this->assertEquals($this->model->$fieldName, 'value');
        
        
        $this->model->$fieldName = 'newValue';
        $this->assertEquals($this->model->$fieldName, $this->field->getValue());
        $this->assertEquals($this->model->$fieldName, 'newValue');
        
        $this->model->removeField($fieldName);
        $this->assertNull($this->model->$fieldName);
    }
    
    public function testGetSetValid()
    {
        $this->assertTrue($this->model->isValid());
        $this->model->setValid(false);
        $this->assertFalse($this->model->isValid());
        $this->model->setValid(true);
        $this->assertTrue($this->model->isValid());
    }
    
    public function testGetValues()
    {
        $this->field->setValue('testValue');
        $this->field2->setValue('testValue2');
        
        $this->model->addField($this->field);
        $this->model->addField($this->field2);
        
        $values = $this->model->getValues();
        
        $expectedValues = array(
            $this->fieldName  => 'testValue',
            $this->fieldName2 => 'testValue2',
        );
        
        $this->assertEquals($expectedValues, $values);
    }
    
    public function testValidate()
    {
        $this->required->expects($this->exactly(2))->method('validate')->will(
            $this->onConsecutiveCalls(true, false)
        );
        
        $this->model->field($this->field);
        $this->model->validate();
        $this->assertTrue($this->model->isValid());
        $this->field->addValidation($this->required);
        $this->model->validate();
        $this->assertTrue($this->model->isValid());
        $this->model->validate();
        $this->assertFalse($this->model->isValid());
    }
    
    /**
     * @dataProvider getSetOptionsProvider
     */
    public function testGetSetOption($option, $value)
    {
        $this->model->setOption($option, $value);
        $this->assertEquals($value, $this->model->getOption($option));
    }
    
    public function testGetSetOptions()
    {
        $options = array(
            'option1' => 'value1',
            'option2' => 'value2',
            'option3' => 'value3'
        );
        $this->assertCount(0, $this->model->getOptions());
        $this->model->setOptions($options);
        $this->assertCount(3, $this->model->getOptions());
        $this->assertEquals($options, $this->model->getOptions());
    }
    
    public function testPopulate()
    {
        $data = array(
            $this->fieldName  => 'value',
            $this->fieldName2 => 'value2'
        );
        
        $testData = $data;
        $testData['notValid'] = 'value3';
        
        
        
        $this->model->addField($this->field);
        $this->model->addField($this->field2);
        
        $this->model->populate($testData);
        $this->assertEquals($data, $this->model->getValues());
        
        foreach ($data as $field => $value)
        {
            $this->assertEquals($value, $this->model->getField($field)->getValue());
        }        
    }
    
    public function testGetErrors()
    {
        $this->required->expects($this->once())->method('validate')->will($this->returnValue(false));
        $this->model->addField($this->field);
        $this->model->validate();
        $this->assertEmpty($this->model->getErrors());
        $this->field->addValidation($this->required);
        $this->model->validate();
        $this->assertNotEmpty($this->model->getErrors());
        $this->assertCount(1, $this->model->getErrors());
        $this->assertArrayHasKey($this->fieldName, $this->model->getErrors());
    }
    
    public function testGetValid()
    {
        $this->field->setValue('');
        $this->field2->setValue('value');
        
        $this->model->addField($this->field);
        $this->model->addField($this->field2);
        $this->model->validate();
        
        $validValues = $this->model->getValidValues();
        $this->assertCount(2, $validValues);        
        $this->assertArrayHasKey($this->fieldName, $validValues);
        $this->assertArrayHasKey($this->fieldName2, $validValues);        
        $this->assertEquals($this->field->getValue(), $validValues[$this->fieldName]);
        $this->assertEquals($this->field2->getValue(), $validValues[$this->fieldName2]);
        
        $this->field->addValidation($this->required);
        $this->model->validate();
        
        $validValues = $this->model->getValidValues();
        $this->assertCount(1, $validValues);        
        $this->assertArrayNotHasKey($this->fieldName, $validValues);
        $this->assertArrayHasKey($this->fieldName2, $validValues);        
    }
    
    public function testGetValuesWithFilter()
    {
        $this->field->setValue('value');
        $this->field2->setValue('value2');
        $this->model->addField($this->field);
        $this->model->addField($this->field2);
        
        $filterTrue = function() {
            return true;
        };
        
        $filterFalse = function() {
            return false;
        };
        
        $fieldName = $this->fieldName;
        
        $filterField1 = function($field) use ($fieldName) {
            return $field->getName() == $fieldName;
        };
        
        $this->assertEmpty($this->model->getValues($filterFalse));
        $this->assertEquals($this->model->getValues(), $this->model->getValues($filterTrue));       
        
        $fields = $this->model->getValues($filterField1);
        
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey($fieldName, $fields);
        $this->assertEquals($this->field->getValue(), $fields[$fieldName]);
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
