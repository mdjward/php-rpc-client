<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test;

use PHPUnit_Framework_TestCase as TestCase;



/**
 * Description of AbstractTestCase
 *
 * @author matt
 */
abstract class AbstractTestCase extends TestCase {
    
    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildGuzzleClientMock() {
        return $this->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildGuzzleRequestMock() {
        return $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildGuzzleResponseMock() {
        return $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
}
