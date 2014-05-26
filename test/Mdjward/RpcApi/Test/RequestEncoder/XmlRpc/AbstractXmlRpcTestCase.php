<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use Mdjward\RpcApi\Test\AbstractTestCase;



/**
 * Description of AbstractXmlRpcTestCase
 *
 * @author matt
 */
abstract class AbstractXmlRpcTestCase extends AbstractTestCase {
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildXmlRpcParameterFactoryMock() {
        return $this->getMockBuilder('Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildXmlRpcParameterSerializerMock() {
        return $this->getMockBuilder('Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterSerializer')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildXmlRpcParameterDeserializerMock() {
        return $this->getMockBuilder('Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterDeserializer')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
}
