<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMXPath;
use ReflectionClass;
use StdClass;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterDeserializer;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterSerializer;



/**
 * Description of XmlRpcParameterDeserializerTest
 *
 * @author matt
 */
class XmlRpcParameterDeserializerTest extends AbstractXmlRpcTestCase {
    
    /**
     * @test
     */
    public function testConstructorAssignsInjectedValues() {
        
        $parameterFactory = $this->buildXmlRpcParameterFactoryMock();
        
        $reflection = new ReflectionClass('Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterDeserializer');
        
        $parameterFactoryProperty = $reflection->getProperty("parameterFactory");
        $parameterFactoryProperty->setAccessible(true);
        
        $forceAssocProperty = $reflection->getProperty("forceAssociativeArraysForStructs");
        $forceAssocProperty->setAccessible(true);
        
        foreach (array(true) as $trueValue) {
            $trueDeserializer = new XmlRpcParameterDeserializer($parameterFactory, $trueValue);
            
            $this->assertSame($parameterFactory, $parameterFactoryProperty->getValue($trueDeserializer));
            $this->assertTrue($forceAssocProperty->getValue($trueDeserializer));
        }
        
        foreach (array(false, 0, "random", 0.123456) as $falseValue) {
            $falseDeserializer1 = new XmlRpcParameterDeserializer($parameterFactory, $falseValue);
            $falseDeserializer2 = new XmlRpcParameterDeserializer($parameterFactory);
            
            $this->assertSame($parameterFactory, $parameterFactoryProperty->getValue($falseDeserializer1));
            $this->assertFalse($forceAssocProperty->getValue($falseDeserializer1));
            
            $this->assertSame($parameterFactory, $parameterFactoryProperty->getValue($falseDeserializer2));
            $this->assertFalse($forceAssocProperty->getValue($falseDeserializer2));
        }
    }
    
    public function testGetXmlRpcParameterFromDomElement() {
        
        $domDocument = new DOMDocument();
        $domDocument->loadXML(
            "<value><string>myname</string></value>"
        );
        
        $parameter = new XmlRpcParameter("myname", XmlRpcParameter::TYPE_STRING);
        
        $parameterFactory = $this->buildXmlRpcParameterFactoryMock();
        $parameterFactory
            ->expects($this->once())
            ->method("fromValue")
            ->with($this->equalTo("myname"))
            ->will($this->returnValue($parameter))
        ;
        
        $deserializer = new XmlRpcParameterDeserializer($parameterFactory);
        
        $this->assertSame(
            $parameter,
            $deserializer->getXmlRpcParameterFromDomElement($domDocument->documentElement)
        );
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesDoubles() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        foreach (array(2.12345, -5.32211) as $value) {
            
            $domDocument = new DOMDocument();
            $domDocument->loadXML("<value><double>{$value}</double></value>");
            
            $this->assertSame($value, $deserializer->getValueFromDomElement($domDocument->documentElement));
        }
        
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesIntegers() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        foreach (array(7, 9) as $value) {
            
            $domDocument = new DOMDocument();
            $domDocument->loadXML("<value><int>{$value}</int></value>");
            
            $this->assertSame($value, $deserializer->getValueFromDomElement($domDocument->documentElement));
        }
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesBooleans() {
        
        $deserialzer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        $domDocument = new DOMDocument();
        
        $domDocument->loadXML("<value><boolean>1</boolean></value>");
        $this->assertTrue($deserialzer->getValueFromDomElement($domDocument->documentElement));
        
        $domDocument->loadXML("<value><boolean>0</boolean></value>");
        $this->assertFalse($deserialzer->getValueFromDomElement($domDocument->documentElement));
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesDateTime() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        $dateTime = new DateTime();
        $iso8601DateTime = $dateTime->format(DateTime::ISO8601);
        
        $domDocument = new DOMDocument();
        $domDocument->loadXML("<value><dateTime.iso8601>{$iso8601DateTime}</dateTime.iso8601></value>");
        
        $returnedValue = $deserializer->getValueFromDomElement($domDocument->documentElement);
        
        $this->assertInstanceOf('\DateTime', $returnedValue);
        $this->assertEquals($dateTime->getTimestamp(), $returnedValue->getTimestamp());
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesStringsAndBase64() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        $values = array(
            "string 1"      =>  XmlRpcParameter::TYPE_STRING,
            "string two"    =>  XmlRpcParameter::TYPE_BASE64
        );
        
        $domDocument = new DOMDocument();
        
        foreach ($values as $value => $type) {
            $domDocument->loadXML("<value><{$type}>{$value}</{$type}></value>");
            $this->assertSame($value, $deserializer->getValueFromDomElement($domDocument->documentElement));
        }
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesNumericArrays() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        $domDocument = new DOMDocument();
        $domDocument->loadXML(
            "<value><array><data><value><string>myname</string></value><value><int>7</int></value><value><boolean>1</boolean></value></data></array></value>"
        );
        
        $this->assertSame(
            array("myname", 7, true),
            $deserializer->getValueFromDomElement($domDocument->documentElement)
        );
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementHandlesObjects() {
        
        $assocDeserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock(), true);
        
        $domDocument = new DOMDocument();
        $domDocument->loadXML(
            "<value><struct><member><name>id</name><value><int>7</int></value></member><member><name>name</name><value><string>myname</string></value></member><member><name>enabled</name><value><boolean>1</boolean></value></member></struct></value>"
        );
        
        $this->assertSame(
            array(
                "id"        =>  7,
                "name"      =>  "myname",
                "enabled"   =>  true
            ),
            $assocDeserializer->getValueFromDomElement($domDocument->documentElement)
        );
        
        $objectDeserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock(), false);
        
        $expectedObject = new StdClass();
        $expectedObject->id = 7;
        $expectedObject->name = "myname";
        $expectedObject->enabled = true;
        
        $this->assertEquals(
            $expectedObject,
            $objectDeserializer->getValueFromDomElement($domDocument->documentElement)
        );
    }
    
    /**
     * @test
     */
    public function testGetValueFromDomElementProducesNullForOtherTypes() {
        
        $deserializer = new XmlRpcParameterDeserializer($this->buildXmlRpcParameterFactoryMock());
        
        $domDocument = new DOMDocument();
        
        foreach (array("nil", "random", "other") as $type) {
            $domDocument->loadXML("<value><{$type}>randomvalue</{$type}></value>");
            $this->assertNull($deserializer->getValueFromDomElement($domDocument->documentElement));
        }
    }
    
}
