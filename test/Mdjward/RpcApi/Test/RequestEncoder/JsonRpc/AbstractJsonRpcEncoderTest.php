<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\JsonRpc;

use ReflectionClass;
use Mdjward\RpcApi\Test\AbstractTestCase;
use Mdjward\RpcApi\RequestEncoder\JsonRpc\AbstractJsonRpcEncoder;



/**
 * Description of AbstractJsonRpcEncoderTest
 *
 * @author matt
 */
class AbstractJsonRpcEncoderTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesValidResponse() {
        
        $responseData = array(
            "result" => "Success"
        );
        
        $response = $this->buildGuzzleResponseMock();
        $response
            ->expects($this->once())
            ->method("json")
            ->with()
            ->will($this->returnValue($responseData))
        ;
        
        $decodedResponse = $this->buildAbstractJsonRpcEncoderMock()->decodeResponse($response);
        
        $this->assertInternalType("string", $decodedResponse);
        
        $this->assertSame(
            $responseData["result"],
            $decodedResponse
        );
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesInvalidResponse() {
        
        $responseData = array(
            "error" => "Error string"
        );
        
        $response = $this->buildGuzzleResponseMock();
        $response
            ->expects($this->once())
            ->method("json")
            ->with()
            ->will($this->returnValue($responseData))
        ;
        
        $this->setExpectedException('Mdjward\RpcApi\RpcErrorException', $responseData['error']);
        $this->buildAbstractJsonRpcEncoderMock()->decodeResponse($response);
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesEmptyResponse() {
        
        $responseData = array(
            "noresult" => "irrelevant"
        );
        
        $response = $this->buildGuzzleResponseMock();
        $response
            ->expects($this->once())
            ->method("json")
            ->with()
            ->will($this->returnValue($responseData))
        ;
        
        $this->setExpectedException('Mdjward\RpcApi\RpcNoResultException');
        $this->buildAbstractJsonRpcEncoderMock()->decodeResponse($response);
    }
    
    /**
     * @test
     */
    public function testProduceJsonEncodableArray() {
        
        $methodName = "Xbmc.VideoLibrary.Scan";
        $parameters = array(1, "two", 3.152989890, true);
        $identifier = 5;
        
        $encoder = $this->buildAbstractJsonRpcEncoderMock();
        $reflection = new ReflectionClass($encoder);
        
        $targetMethod = $reflection->getMethod("produceJsonEncodeableArray");
        $targetMethod->setAccessible(true);
        
        $this->assertSame(
            array(
                "method" => $methodName,
                "params" => $parameters,
                "id" => $identifier
            ),
            $targetMethod->invokeArgs($encoder, array($methodName, $parameters, $identifier))
        );
    }
    
    /**
     * @test
     */
    public function testEncodeRequestUsesJsonEncode() {
        
        $encoder = $this->buildAbstractJsonRpcEncoderMock();
        $reflection = new ReflectionClass($encoder);
        
        $targetMethod = $reflection->getMethod("produceJsonEncodeableArray");
        $targetMethod->setAccessible(true);
        
        $methodName = "method";
        $parameters = array(1, 2, 'three', true);
        $identifier = 4;
        
        $methodArguments = array(
            "method" => $methodName,
            "params" => $parameters,
            "id" => $identifier
        );
        
        $targetMethod->invokeArgs($encoder, $methodArguments);
        
        $this->assertSame(
            json_encode($methodArguments),
            $encoder->encodeRequest($methodName, $parameters, $identifier)
        );
    }
    
    /**
     * 
     * @return Mdjward\RpcApi\RequestEncoder\JsonRpc\AbstractJsonRpcEncoder
     */
    protected function buildAbstractJsonRpcEncoderMock() {
        return $this->getMockBuilder('Mdjward\RpcApi\RequestEncoder\JsonRpc\AbstractJsonRpcEncoder')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
    
}
