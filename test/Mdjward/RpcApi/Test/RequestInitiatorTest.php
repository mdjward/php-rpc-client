<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test;

use ReflectionClass;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Mdjward\RpcApi\RequestInitiator;



/**
 * Description of RequestInitiatorTest
 *
 * @author matt
 */
class RequestInitiatorTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testConstructorAssignsInjectedObjects() {
        
        $encoder = $this->buildRpcEncoderMock();
        $client = new Client();
        $namespace = "Xbmc.VideoLibrary";
        
        $initiator = new RequestInitiator(
            $encoder,
            $client,
            $namespace
        );
        
        $reflection = new ReflectionClass($initiator);
        
        $encoderProperty = $reflection->getProperty("encoder");
        $encoderProperty->setAccessible(true);
        
        $clientProperty = $reflection->getProperty("client");
        $clientProperty->setAccessible(true);
        
        $namespaceProperty = $reflection->getProperty("namespace");
        $namespaceProperty->setAccessible(true);
        
        $this->assertSame($encoder, $encoderProperty->getValue($initiator));
        $this->assertSame($client, $clientProperty->getValue($initiator));
        $this->assertSame($namespace, $namespaceProperty->getValue($initiator));
    }
    
    /**
     * @test
     */
    public function testMagicGetReturnsNewInitiator() {
        
        $encoder = $this->buildRpcEncoderMock();
        $client = new Client();
        $parentNamespace = "Xbmc.VideoLibrary";
        $childNamespace = "Test";
        
        $initiator = new RequestInitiator(
            $encoder,
            $client,
            $parentNamespace
        );
        
        $reflection = new ReflectionClass(get_class($initiator));
        
        $encoderProperty = $reflection->getProperty("encoder");
        $encoderProperty->setAccessible(true);
        
        $clientProperty = $reflection->getProperty("client");
        $clientProperty->setAccessible(true);
        
        $namespaceProperty = $reflection->getProperty("namespace");
        $namespaceProperty->setAccessible(true);
        
        $createNamespaceMethod = $reflection->getMethod("createNamespace");
        $createNamespaceMethod->setAccessible(true);
        
        $newInitiator = $initiator->__get($childNamespace);
        
        $this->assertSame(
            $encoderProperty->getValue($initiator),
            $encoderProperty->getValue($newInitiator)
        );
        
        $this->assertSame(
            $encoderProperty->getValue($initiator),
            $encoderProperty->getValue($newInitiator)
        );
        
        $this->assertSame(
            $createNamespaceMethod->invokeArgs($initiator, array($childNamespace)),
            $namespaceProperty->getValue($newInitiator)
        );
    }
    
    public function testMagicCall() {
        
        $methodName = "Xbmc.VideoLibrary.Scan";
        $parameters = array(1, "two", 3.12345, false);
        $requestBody = "Request body";
        $responseBody = "Response body";
        
        $response = new Response(200, null, $responseBody);
        
        $encoder = $this->buildRpcEncoderMock();
        $encoder
            ->expects($this->once())
            ->method("encodeRequest")
            ->with(
                $this->identicalTo($methodName),
                $this->identicalTo($parameters),
                $this->equalTo(1)
            )
            ->will($this->returnValue($requestBody))
        ;
        $encoder
            ->expects($this->once())
            ->method("decodeResponse")
            ->with($this->identicalTo($response))
            ->will($this->returnValue($responseBody))
        ;
        
        $request = $this->buildGuzzleRequestMock();
        $request
            ->expects($this->once())
            ->method("send")
            ->with()
            ->will($this->returnValue($response))
        ;
        
        $client = $this->buildGuzzleClientMock();
        $client
            ->expects($this->once())
            ->method("post")
            ->with(
                $this->isNull(),
                $this->isType("array"),
                $this->identicalTo($requestBody)
            )
            ->will($this->returnValue($request))
        ;
        
        $initiator = new RequestInitiator($encoder, $client);
        $reflection = new ReflectionClass($initiator);
        
        $requestIdProperty = $reflection->getProperty("requestId");
        $requestIdProperty->setAccessible(true);
        
        $result = $initiator->__call($methodName, $parameters);
        
        $this->assertSame($responseBody, $result);
    }
    
    /**
     * @test
     */
    public function testGetClient() {
        
        $client = new Client();
        
        $initiator = new RequestInitiator(
            $this->buildRpcEncoderMock(),
            $client
        );
        
        $this->assertSame($client, $initiator->getClient());
    }
    
    /**
     * @test
     */
    public function testCreateNamespaceSuffixesNewNamespace() {
        
        $parentNamespace = "Xbmc";
        $childNamespace = "VideoLibrary";
        
        $initiator = new RequestInitiator(
            $this->buildRpcEncoderMock(),
            new Client(),
            $parentNamespace
        );
        
        $reflection = new ReflectionClass($initiator);
        
        $getNamespaceSeparatorMethod = $reflection->getMethod("getNamespaceSeparator");
        $getNamespaceSeparatorMethod->setAccessible(true);
        
        $createNamespaceMethod = $reflection->getMethod("createNamespace");
        $createNamespaceMethod->setAccessible(true);
        
        $this->assertSame(
            $parentNamespace . $getNamespaceSeparatorMethod->invoke(null) . $childNamespace,
            $createNamespaceMethod->invokeArgs($initiator, array($childNamespace))
        );
    }
    
    /**
     * @test
     */
    public function testGetNamespaceSeperatorPassesConstant() {
        
        $reflection = new ReflectionClass('Mdjward\RpcApi\RequestInitiator');
        
        $getNamespaceSeparatorMethod = $reflection->getMethod("getNamespaceSeparator");
        $getNamespaceSeparatorMethod->setAccessible(true);
        
        $this->assertSame(RequestInitiator::NAMESPACE_SEPARATOR, $getNamespaceSeparatorMethod->invoke(null));
    }
    
    /**
     * @test
     */
    public function testGetRequiredHeadersReturnsEmptyArray() {
        
        $initiator = new RequestInitiator(
            $this->buildRpcEncoderMock(),
            new Client()
        );
        
        $reflection = new ReflectionClass($initiator);
        
        $getRequiredHeadersMethod = $reflection->getMethod("getRequiredHeaders");
        $getRequiredHeadersMethod->setAccessible(true);
        
        $headers = $getRequiredHeadersMethod->invoke($initiator);
        
        $this->assertTrue(is_array($headers));
        $this->assertCount(0, $headers);
    }
    
    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildRpcEncoderMock() {
        return $this->getMockBuilder('Mdjward\RpcApi\RequestEncoder\RpcEncoder')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
    
}
