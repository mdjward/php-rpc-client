<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\JsonRpc;

use ReflectionClass;
use Mdjward\RpcApi\RequestEncoder\JsonRpc\JsonRpcV1Encoder;
use Mdjward\RpcApi\Test\AbstractTestCase;



/**
 * Description of JsonRpcV1EncoderTest
 *
 * @author matt
 */
class JsonRpcV1EncoderTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testProduceJsonEncodeableArrayDestroysArrayKeys() {
        
        $encoder = new JsonRpcV1Encoder();
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
            range(0, count($parameters) - 1),
            array_keys($result["params"])
        );
        
        $this->assertSame(
            array(
                "method" => $method,
                "params" => array_values($parameters),
                "id" => $identifier
            ),
            $result
        );
    }
    
}
