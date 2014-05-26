<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use DOMDocument;
use DOMDocumentType;
use DOMImplementation;
use ReflectionClass;
use Guzzle\Http\Message\Response;
use Mdjward\RpcApi\RpcNoResultException;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcEncoder;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter;

/**
 * Description of XmlRpcEncoderTest
 *
 * @author matt
 */
class XmlRpcEncoderTest extends AbstractXmlRpcTestCase {
    
    /**
     * @test
     */
    public function testConstructorAssignsInjectedObjects() {
        
        $parameterFactory = $this->buildXmlRpcParameterFactoryMock();
        $serializer = $this->buildXmlRpcParameterSerializerMock();
        $deserializer = $this->buildXmlRpcParameterDeserializerMock();
        $domImplementation = $this->buildDomImplementationMock();
        
        $encoder = new XmlRpcEncoder(
            $parameterFactory,
            $serializer,
            $deserializer,
            $domImplementation
        );
        
        $reflection = new ReflectionClass($encoder);
        
        $parameterFactoryProperty = $reflection->getProperty("parameterFactory");
        $parameterFactoryProperty->setAccessible(true);
        
        $serializerProperty = $reflection->getProperty("parameterSerializer");
        $serializerProperty->setAccessible(true);
        
        $deserializerProperty = $reflection->getProperty("parameterDeserializer");
        $deserializerProperty->setAccessible(true);
        
        $domImplementationProperty = $reflection->getProperty("domImplementation");
        $domImplementationProperty->setAccessible(true);
        
        $this->assertSame($parameterFactory, $parameterFactoryProperty->getValue($encoder));
        $this->assertSame($serializer, $serializerProperty->getValue($encoder));
        $this->assertSame($deserializer, $deserializerProperty->getValue($encoder));
        $this->assertSame($domImplementation, $domImplementationProperty->getValue($encoder));
    }
    
    /**
     * @test
     */
    public function testEncodeRequest() {
        
        $methodName = "Xbmc.VideoLibrary.Update";
        
        $parameterMap = array(
            1           =>  new XmlRpcParameter(1, XmlRpcParameter::TYPE_INTEGER),
            "myname"    =>  new XmlRpcParameter("myname", XmlRpcParameter::TYPE_STRING)
        );
        
        $totalValues = count($parameterMap);
        
        $parameterFactory = $this->buildXmlRpcParameterFactoryMock();
        $parameterFactory
            ->expects($this->exactly($totalValues))
            ->method("fromValue")
            ->will($this->returnCallback(
                function($value) use ($parameterMap) {
                    if (isset($parameterMap[$value])) {
                        return $parameterMap[$value];
                    }
                    
                    return null;
                }
            ))
        ;
        
        $domDocument1 = new DOMDocument();
        $domDocument1->loadXML("<value><int>1</int></value>");
        $domDocument2 = new DOMDocument();
        $domDocument2->loadXML("<value><string></string></value>");

        $serializerMap = array(
            1           =>  $domDocument1->documentElement,
            "myname"    =>  $domDocument2->documentElement
        );
        
        $serializer = $this->buildXmlRpcParameterSerializerMock();
        $serializer
            ->expects($this->exactly($totalValues))
            ->method("getDomElementFromParameter")
            ->will($this->returnCallback(
                function(XmlRpcParameter $parameter) use ($serializerMap) {
                    $parameterValue = $parameter->getValue();
                    
                    if (isset($serializerMap[$parameterValue])) {
                        return $serializerMap[$parameterValue];
                    }
                    
                    return null;
                }
            ))
        ;
        
        $encoder = new XmlRpcEncoder(
            $parameterFactory,
            $serializer,
            $this->buildXmlRpcParameterDeserializerMock(),
            new DOMImplementation()
        );
        
        $encodedXml = $encoder->encodeRequest($methodName, array_keys($parameterMap));
        
        $encodedDomDocument = new DOMDocument();
        $encodedDomDocument->loadXML($encodedXml);
        
        $expectedDomDocument = new DOMDocument();
        $expectedDomDocument->loadXML(
            "<methodCall>"
            . "<methodName>{$methodName}</methodName>"
            . "<params>"
            . "<param>" . $domDocument1->saveXML($domDocument1->documentElement) . "</param>"
            . "<param>" . $domDocument2->saveXML($domDocument2->documentElement) . "</param>"
            . "</params>"
            . "</methodCall>"
        );
            
        $this->assertEqualXMLStructure(
            $expectedDomDocument->documentElement,
            $encodedDomDocument->documentElement
        );
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesSuccessfulResponse() {
        
        $expectedValue = 42;
        
        $response = new Response(
            200,
            null,
            "<methodResponse>"
            . "<params>"
            . "<param><value><int>{$expectedValue}</int></value></param>"
            . "</params>"
            . "</methodResponse>"
        );
        
        $deserializerMock = $this->buildXmlRpcParameterDeserializerMock();
        $deserializerMock
            ->expects($this->once())
            ->method("getValueFromDomElement")
            ->with($this->isInstanceOf('\DOMElement'))
            ->will($this->returnValue($expectedValue))
        ;
        
        $encoder = new XmlRpcEncoder(
            $this->buildXmlRpcParameterFactoryMock(),
            $this->buildXmlRpcParameterSerializerMock(),
            $deserializerMock,
            $this->buildDomImplementationMock()
        );
        
        $decodedResponse = $encoder->decodeResponse($response);
        
        $this->assertInternalType("array", $decodedResponse);
        $this->assertSame(array($expectedValue), $decodedResponse);
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesUnsuccessfulResponse() {
        
        $faultCode = 343;
        $faultMessage = "unacceptable failure";
        
        $response = new Response(
            200,
            null,
            "<methodResponse>"
            . "<fault>"
            . "<value><struct>"
            . "<member><name>faultCode</name><value><int>{$faultCode}</int></value></member>"
            . "<member><name>faultString</name><value><string>{$faultMessage}</string></value></member>"
            . "</struct></value>"
            . "</fault>"
            . "</methodResponse>"
        );
            
        $this->setExpectedException('Mdjward\RpcApi\RpcErrorException', $faultMessage, $faultCode);
        
        $encoder = new XmlRpcEncoder(
            $this->buildXmlRpcParameterFactoryMock(),
            $this->buildXmlRpcParameterSerializerMock(),
            $this->buildXmlRpcParameterDeserializerMock(),
            $this->buildDomImplementationMock()
        );
        
        $encoder->decodeResponse($response);
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesEmptyResponse() {
        
        $response = new Response(
            200,
            null,
            "<random><xml></xml></random>"
        );
        
        $this->setExpectedException('Mdjward\RpcApi\RpcNoResultException', RpcNoResultException::EXCEPTION_MESSAGE);
        
        $encoder = new XmlRpcEncoder(
            $this->buildXmlRpcParameterFactoryMock(),
            $this->buildXmlRpcParameterSerializerMock(),
            $this->buildXmlRpcParameterDeserializerMock(),
            $this->buildDomImplementationMock()
        );
        
        $encoder->decodeResponse($response);
    }
    
    /**
     * @test
     */
    public function testBuildDocument() {
        
        $domDocument = new DOMDocument();
        
        $domImplementation = $this->buildDomImplementationMock();
        $domImplementation
            ->expects($this->once())
            ->method("createDocument")
            ->with()
            ->will($this->returnValue($domDocument))
        ;
        
        $encoder = new XmlRpcEncoder(
            $this->buildXmlRpcParameterFactoryMock(),
            $this->buildXmlRpcParameterSerializerMock(),
            $this->buildXmlRpcParameterDeserializerMock(),
            $domImplementation
        );
        
        $reflection = new ReflectionClass($encoder);
        
        $buildDocumentMethod = $reflection->getMethod("buildDocument");
        $buildDocumentMethod->setAccessible(true);
        
        $returnedDocument = $buildDocumentMethod->invoke($encoder);
        
        $this->assertSame($returnedDocument, $domDocument);
        $this->assertSame("UTF-8", $returnedDocument->encoding);
        $this->assertSame("1.0", $returnedDocument->version);
    }
    
    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildDomImplementationMock() {
        return $this->getMockBuilder(__NAMESPACE__ . '\DomImplementationMockStub')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
}



class DomImplementationMockStub extends DOMImplementation {
    
    public function createDocument($namespaceURI = null, $qualifiedName = null, DOMDocumentType $doctype = null) {
        return parent::createDocument($namespaceURI, $qualifiedName, $doctype);
    }
    
}