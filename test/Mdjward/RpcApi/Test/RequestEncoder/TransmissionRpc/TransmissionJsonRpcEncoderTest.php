<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\TransmissionRpc;

use Mdjward\RpcApi\RequestEncoder\TransmissionRpc\TransmissionJsonRpcEncoder;
use Mdjward\RpcApi\Test\AbstractTestCase;

/**
 * Description of TransmissionJsonRpcEncoderTest
 *
 * @author matt
 */
class TransmissionJsonRpcEncoderTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testEncodeRequestIgnoresNullIdentifier() {
        
        $methodName = "GetTorrents";
        $parameters = array(1, "name");
        
        $encoder = new TransmissionJsonRpcEncoder();
        
        $this->assertSame(
            json_encode(
                array(
                    "method" => $methodName,
                    "arguments" => $parameters
                )
            ),
            $encoder->encodeRequest($methodName, $parameters)
        );
    }
    
    /**
     * @test
     */
    public function testEncodeRequestAddsTagForIdentifier() {
        
        $methodName = "GetTorrents";
        $parameters = array(1, "name");
        $tag = 7;
        
        $encoder = new TransmissionJsonRpcEncoder();
        
        $this->assertSame(
            json_encode(
                array(
                    "method" => $methodName,
                    "arguments" => $parameters,
                    "tag" => $tag
                )
            ),
            $encoder->encodeRequest($methodName, $parameters, $tag)
        );
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesValidResponse() {
        
        $responseData = array(
            "result" => "success",
            "arguments" => array("one", "two")
        );
        
        $response = $this->buildGuzzleResponseMock();
        $response
            ->expects($this->once())
            ->method("json")
            ->with()
            ->will($this->returnValue($responseData))
        ;
        
        $encoder = new TransmissionJsonRpcEncoder();
        $decodedResponse = $encoder->decodeResponse($response);
        
        $this->assertInternalType("array", $decodedResponse);
        
        $this->assertSame(
            $responseData["arguments"],
            $decodedResponse
        );
    }
    
    /**
     * @test
     */
    public function testDecodeResponseHandlesInvalidResponse() {
        
        $responseData = array(
            "result" => "failure"
        );
        
        $response = $this->buildGuzzleResponseMock();
        $response
            ->expects($this->once())
            ->method("json")
            ->with()
            ->will($this->returnValue($responseData))
        ;
        
        $this->setExpectedException('Mdjward\RpcApi\RpcErrorException', $responseData['result']);
        
        $encoder = new TransmissionJsonRpcEncoder();
        $encoder->decodeResponse($response);
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
        
        $encoder = new TransmissionJsonRpcEncoder();
        $encoder->decodeResponse($response);
    }
    
    
}
