<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Mdjward\RpcApi\RpcNoResultException;



/**
 * Description of RpcNoResultExceptionTest
 *
 * @author matt
 */
class RpcNoResultExceptionTest extends TestCase {
    
    /**
     * @test
     */
    public function testExceptionConstructor() {
        
        $exception = new RpcNoResultException();
        
        $this->assertSame(RpcNoResultException::EXCEPTION_MESSAGE, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
    
}
