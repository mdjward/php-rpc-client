<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use DateTime;
use StdClass;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory;



/**
 * Description of XmlRpcParameterFactoryTest
 *
 * @author matt
 */
class XmlRpcParameterFactoryTest extends AbstractXmlRpcTestCase {
    
    /**
     * @test
     */
    public function testFromValueHandlesNull() {
        
        $parameter = $this->buildParameterFactory()->fromValue(null);
                
        $this->assertNull($parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_NIL, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesDateTimeObject() {
        
        $dateTime = new DateTime();
        
        $parameter = $this->buildParameterFactory()->fromValue($dateTime);
        
        $this->assertSame($dateTime->format(DateTime::ISO8601), $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_DATETIME, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesIso8601DateTimeString() {
        
        $dateTime = new DateTime();
        
        $parameter = $this->buildParameterFactory()->fromValue($dateTime->format(DateTime::ISO8601));
        
        $this->assertSame($dateTime->format(DateTime::ISO8601), $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_DATETIME, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesObjectAsStruct() {
        
        $object = new StdClass();
        $object->id = 1;
        $object->name = "myname";
        
        $parameter = $this->buildParameterFactory()->fromValue($object);
        
        $this->assertSame($object, $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_STRUCT, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesAssociativeArrayAsStruct() {
        
        $array = array(
            "my" => "name",
            "is" => "A Test Case"
        );
        
        $parameter = $this->buildParameterFactory()->fromValue($array);
        
        $this->assertSame($array, $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_STRUCT, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesNumericArrayAsArray() {
        
        $array = array(
            "my",
            "name",
            "is",
            "A Test Case"
        );
        
        $parameter = $this->buildParameterFactory()->fromValue($array);
        
        $this->assertSame($array, $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_ARRAY, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesNumericValues() {
        
        $parameterFactory = $this->buildParameterFactory();
        
        $nonIntegerValue = 1.2345;
        
        $parameter = $parameterFactory->fromValue($nonIntegerValue);
        
        $this->assertSame($nonIntegerValue, $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_DOUBLE, $parameter->getType());
        
        $integerValues = array(
            2.00000,
            5
        );
        
        foreach ($integerValues as $integerValue) {
            $parameter = $parameterFactory->fromValue($integerValue);

            $this->assertSame((int) $integerValue, $parameter->getValue());
            $this->assertSame(XmlRpcParameter::TYPE_INTEGER, $parameter->getType());
        }
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesBooleanValues() {
        
        $parameterFactory = $this->buildParameterFactory();
        
        $trueParameter = $parameterFactory->fromValue(true);
        
        $this->assertTrue($trueParameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_BOOLEAN, $trueParameter->getType());
        
        $falseParameter = $parameterFactory->fromValue(false);
        
        $this->assertFalse($falseParameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_BOOLEAN, $falseParameter->getType());
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesStringBooleans() {
        
        $parameterFactory = $this->buildParameterFactory();
        
        foreach (array("true", "TRUE", "tRuE") as $trueValue) {
            $trueParameter = $parameterFactory->fromValue($trueValue);

            $this->assertTrue($trueParameter->getValue());
            $this->assertSame(XmlRpcParameter::TYPE_BOOLEAN, $trueParameter->getType());
        }
            
        foreach (array("false", "FALSE", "fAlSe") as $falseValue) {
            $falseParameter = $parameterFactory->fromValue($falseValue);

            $this->assertFalse($falseParameter->getValue());
            $this->assertSame(XmlRpcParameter::TYPE_BOOLEAN, $falseParameter->getType());
        }
    }
    
    /**
     * @test
     */
    public function testFromValueHandlesStrings() {
        
        $stringValue = "othervalue";
        
        $parameter = $this->buildParameterFactory()->fromValue($stringValue);
        
        $this->assertSame($stringValue, $parameter->getValue());
        $this->assertSame(XmlRpcParameter::TYPE_STRING, $parameter->getType());
    }
    
    /**
     * 
     * @return \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory
     */
    protected function buildParameterFactory() {
        return new XmlRpcParameterFactory();
    }
    
}
