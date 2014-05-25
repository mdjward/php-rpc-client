<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\JsonRpc;

use ReflectionClass;
use Mdjward\RpcApi\RequestEncoder\JsonRpc\JsonRpcV2Encoder;
use Mdjward\RpcApi\Test\AbstractTestCase;



/**
 * Description of JsonRpcV2EncoderTest
 *
 * @author matt
 */
class JsonRpcV2EncoderTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testProduceJsonEncodeableArray() {
        
        $encoder = new JsonRpcV2Encoder();
        $reflection = new ReflectionClass($encoder);
        
        $method = "method";
        $parameters = array(
            'first'     =>  1,
            'second'    =>  2,
            'third'     => 'three',
            'fourth'    =>  true,
            'fifth'     =>  5.1523
        );
        $identifier = 7;
        
        $targetMethod = $reflection->getMethod("produceJsonEncodeableArray");
        $targetMethod->setAccessible(true);
        
        $result = $targetMethod->invokeArgs($encoder, array($method, $parameters, $identifier));
        
        $this->assertSame(
            array(
                "method" => $method,
                "params" => $parameters,
                "id" => $identifier,
                "jsonrpc" => "2.0"
            ),
            $result
        );
    }
    
}
