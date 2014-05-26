<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mdjward\RpcApi\Test\RequestEncoder\XmlRpc;

use ReflectionClass;
use Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter;
use Mdjward\RpcApi\Test\AbstractTestCase;



/**
 * Description of XmlRpcParameterTest
 *
 * @author matt
 */
class XmlRpcParameterTest extends AbstractTestCase {
    
    /**
     * @test
     */
    public function testConstructorAssignsInjectedObjects() {
        
        $value = "string";
        $type = XmlRpcParameter::TYPE_STRING;
        
        $parameter = new XmlRpcParameter($value, $type);
        
        $reflection = new ReflectionClass($parameter);
        
        $valueProperty = $reflection->getProperty("value");
        $valueProperty->setAccessible(true);

        $typeProperty = $reflection->getProperty("type");
        $typeProperty->setAccessible(true);
        
        $this->assertSame($value, $valueProperty->getValue($parameter));
        $this->assertSame($type, $typeProperty->getValue($parameter));
    }
    
    /**
     * @test
     */
    public function testGetters() {
        
        $newValue = 2;
        $newType = XmlRpcParameter::TYPE_INTEGER;
        
        $parameter = new XmlRpcParameter("string", XmlRpcParameter::TYPE_STRING);
        
        $reflection = new ReflectionClass($parameter);
        
        $valueProperty = $reflection->getProperty("value");
        $valueProperty->setAccessible(true);
        $valueProperty->setValue($parameter, $newValue);

        $typeProperty = $reflection->getProperty("type");
        $typeProperty->setAccessible(true);
        $typeProperty->setValue($parameter, $newType);
        
        $this->assertSame($newValue, $parameter->getValue());
        $this->assertSame($newType, $parameter->getType());
    }
    
    /**
     * @test
     */
    public function testValidateTypeHandlesValidValues() {
        
        $validateTypeMethod = $this->getValidateTypeReflectionMethod();
        
        $validTypes = array(
            XmlRpcParameter::TYPE_ARRAY,
            XmlRpcParameter::TYPE_BASE64,
            XmlRpcParameter::TYPE_BOOLEAN,
            XmlRpcParameter::TYPE_DATETIME,
            XmlRpcParameter::TYPE_DOUBLE,
            XmlRpcParameter::TYPE_INTEGER,
            XmlRpcParameter::TYPE_NIL,
            XmlRpcParameter::TYPE_STRING,
            XmlRpcParameter::TYPE_STRUCT,
        );
        
        foreach ($validTypes as $type) {
            $this->assertSame(
                $type,
                $validateTypeMethod->invokeArgs(null, array($type))
            );
        }
    }
    
    /**
     * @test
     */
    public function testValidateTypeHandlesNonStringValues() {
        
        $this->setExpectedException('\InvalidArgumentException', 'Type must be given as a string only');
        $this->getValidateTypeReflectionMethod()->invokeArgs(null, array(12345));
    }
    
    /**
     * @test
     */
    public function testValidateTypeHandlesInvalidValues() {
        
        $badType = "badtype";
        
        $this->setExpectedException('\InvalidArgumentException', "Invalid type '{$badType}'");
        $this->getValidateTypeReflectionMethod()->invokeArgs(null, array($badType));
    }
    
    /**
     * 
     * @return \ReflectionMethod
     */
    protected function getValidateTypeReflectionMethod() {
        
        $reflection = new ReflectionClass('Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter');
        
        $validateTypeMethod = $reflection->getMethod("validateType");
        $validateTypeMethod->setAccessible(true);
        
        return $validateTypeMethod;
    }
    
}
