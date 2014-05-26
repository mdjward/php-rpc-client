<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use DateTime;
use DOMDocument;
use DOMXPath;
use ReflectionClass;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterSerializer;



/**
 * Description of XmlRpcParameterSerializerTest
 *
 * @author matt
 */
class XmlRpcParameterSerializerTest extends AbstractXmlRpcTestCase {
    
    /**
     * @test
     */
    public function testConstructorAssignsInjectedObjects() {
        
        $parameterFactory = $this->buildXmlRpcParameterFactoryMock();
        $domDocument = new DOMDocument();
        
        $serializer = new XmlRpcParameterSerializer($parameterFactory, $domDocument);
        $reflection = new ReflectionClass($serializer);
        
        $factoryProperty = $reflection->getProperty("parameterFactory");
        $factoryProperty->setAccessible(true);
        
        $domDocumentProperty = $reflection->getProperty("domDocument");
        $domDocumentProperty->setAccessible(true);
        
        $this->assertSame($parameterFactory, $factoryProperty->getValue($serializer));
        $this->assertSame($domDocument, $domDocumentProperty->getValue($serializer));
    }
    
    /**
     * @test
     */
    public function testGetDomElementFromParameterWithNonComplexValues() {
        
        $dateTime = new DateTime();
        
        $parameters = array(
            new XmlRpcParameter("base64-string", XmlRpcParameter::TYPE_BASE64),
            new XmlRpcParameter("string", XmlRpcParameter::TYPE_STRING),
            new XmlRpcParameter(true, XmlRpcParameter::TYPE_BOOLEAN),
            new XmlRpcParameter($dateTime->format(DateTime::ISO8601), XmlRpcParameter::TYPE_DATETIME),
            new XmlRpcParameter(5.12345, XmlRpcParameter::TYPE_DOUBLE),
            new XmlRpcParameter(3, XmlRpcParameter::TYPE_INTEGER)
        );
        
        $serializer = new XmlRpcParameterSerializer($this->buildXmlRpcParameterFactoryMock(), new DOMDocument());
        
        foreach ($parameters as $parameter) {
            
            $element = $serializer->getDomElementFromParameter($parameter);
            
            $this->assertInstanceOf('\DOMElement', $element);
            $this->assertEquals($parameter->getType(), $element->nodeName);
            
            $this->assertEquals(1, $element->childNodes->length);
            $this->assertInstanceOf('\DOMText', $element->firstChild);
            
            $this->assertEquals($parameter->getValue(), $element->textContent);
        }
        
        $nillParameter = new XmlRpcParameter(null, XmlRpcParameter::TYPE_NIL);
        $nillElement = $serializer->getDomElementFromParameter($nillParameter);
        
        $this->assertInstanceOf('\DOMElement', $nillElement);
        $this->assertEquals(XmlRpcParameterSerializer::NIL_ELEMENT_NAME, $nillElement->nodeName);
    }
    
    /**
     * @test
     */
    public function testGetDomElementFromParameterHandlesArray() {
        
        $domDocument = new DOMDocument();
        
        $arrayValues = array(1, 2.0, "three", false, 5.123466, true);
        $arrayParameter = new XmlRpcParameter($arrayValues, XmlRpcParameter::TYPE_ARRAY);
        
        $parameterFactoryReal = new XmlRpcParameterFactory();
        $parameterFactoryMock = $this->buildXmlRpcParameterFactoryMock();
        $parameterFactoryMock
            ->expects($this->exactly(count($arrayValues)))
            ->method("fromValue")
            ->will($this->returnCallback(
                function($value) use ($parameterFactoryReal) {
                    return $parameterFactoryReal->fromValue($value);
                }
            ))
        ;
        
        $serializer = new XmlRpcParameterSerializer($parameterFactoryMock, $domDocument);
        
        $arrayElement = $serializer->getDomElementFromParameter($arrayParameter);
        
        $this->assertInstanceOf('\DOMElement', $arrayElement);
        $this->assertSame("array", $arrayElement->nodeName);
        
        $comparitiveDomDocument = new DOMDocument();
        $comparitiveDomDocument->loadXML(
            "<array><data>"
            . "<value><int>1</int></value>"
            . "<value><int>2</int></value>"
            . "<value><string>three</string></value>"
            . "<value><boolean>false</boolean></value>"
            . "<value><double>5.123466</double></value>"
            . "<value><boolean>true</boolean></value>"
            . "</data></array>"
        );
        
        $this->assertEqualXMLStructure($comparitiveDomDocument->documentElement, $arrayElement);
    }
    
    /**
     * @test
     */
    public function testGetDomElementFromParameterHandlesStruct() {
        
        $domDocument = new DOMDocument();
        
        $arrayValues = array(
            "one"   =>  1,
            "two"   =>  2.0,
            "three" =>  "three",
            "four"  =>  false,
            "five"  =>  5.123466,
            "SIX"   =>  true
        );
        
        $structParameter = new XmlRpcParameter($arrayValues, XmlRpcParameter::TYPE_STRUCT);
        
        $parameterFactoryReal = new XmlRpcParameterFactory();
        $parameterFactoryMock = $this->buildXmlRpcParameterFactoryMock();
        $parameterFactoryMock
            ->expects($this->exactly(count($arrayValues)))
            ->method("fromValue")
            ->will($this->returnCallback(
                function($value) use ($parameterFactoryReal) {
                    return $parameterFactoryReal->fromValue($value);
                }
            ))
        ;
        
        $serializer = new XmlRpcParameterSerializer($parameterFactoryMock, $domDocument);
        
        $structElement = $serializer->getDomElementFromParameter($structParameter);
        
        $comparitiveDomDocument = new DOMDocument();
        $comparitiveDomDocument->loadXML(
            "<struct>"
            . "<member><name>one</name><value><int>1</int></value></member>"
            . "<member><name>two</name><value><int>2</int></value></member>"
            . "<member><name>three</name><value><string>three</string></value></member>"
            . "<member><name>four</name><value><boolean>false</boolean></value></member>"
            . "<member><name>five</name><value><double>5.123466</double></value></member>"
            . "<member><name>SIX</name><value><boolean>true</boolean></value></member>"
            . "</struct>"
        );
        
        $this->assertEqualXMLStructure($comparitiveDomDocument->documentElement, $structElement);
    }
        
}
