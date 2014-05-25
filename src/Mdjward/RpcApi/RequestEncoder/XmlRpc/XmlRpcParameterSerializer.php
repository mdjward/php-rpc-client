<?php

/**
 * XmlRpcParameterSerializer.php
 * Definition of class XmlRpcParameterSerializer
 * 
 * Created 06-Apr-2014 16:17:26
 *
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 * @copyright (c) 2014, Byng Systems/SkillsWeb Ltd
 */

namespace Mdjward\RpcApi\RequestEncoder\XmlRpc;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMText;



/**
 * XmlRpcParameterSerializer
 * 
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 */
class XmlRpcParameterSerializer {

    /**
     *
     * @var \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory
     */
    protected $parameterFactory;
    
    /**
     *
     * @var \DOMDocument
     */
    private $domDocument;
    
    
    
    /**
     * 
     * @param \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory $parameterFactory
     * @param \DOMDocument $domDocument
     */
    public function __construct(XmlRpcParameterFactory $parameterFactory, DOMDocument $domDocument) {
        
        $this->parameterFactory = $parameterFactory;
        $this->domDocument = $domDocument;
    }
    
    /**
     * 
     * @param \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter $parameter
     * @return \DOMElement
     */
    public function getDomElementFromParameter(XmlRpcParameter $parameter) {
        
        switch (($type = $parameter->getType())) {
            case XmlRpcParameter::TYPE_ARRAY:
                return $this->formatArray($parameter);
                
            case XmlRpcParameter::TYPE_STRUCT:
                return $this->formatStruct($parameter);
        }
        
        $typeElement = $this->domDocument->createElement($type);
        $typeElement->appendChild(new DOMText($parameter->getValue()));
        
        return $typeElement;
    }
    
    private function formatStruct(XmlRpcParameter $parameter) {
        
        $typeElement = $this->domDocument->createElement($parameter->getType());
        
        foreach($parameter->getValue() as $name => $value) {
            
            $subParameter = $this->parameterFactory->fromValue($value);
            
            $memberElement = $this->domDocument->createElement("member");
            
            $nameElement = $this->domDocument->createElement("name");
            $nameElement->appendChild(new DOMText($name));
            
            $valueElement = $this->domDocument->createElement("value");
            $valueElement->appendChild($this->getDomElementFromParameter($subParameter));
            
            $memberElement->appendChild($nameElement);
            $memberElement->appendChild($valueElement);
            $typeElement->appendChild($memberElement);
        }
        
        return $typeElement;
    }
    
    private function formatArray(XmlRpcParameter $parameter) {
        
        $typeElement = $this->domDocument->createElement($parameter->getType());
        $dataElement = $this->domDocument->createElement("data");
        
        foreach ($parameter->getValue() as $value) {
            
            $subParameter = $this->parameterFactory->fromValue($value);
            
            $valueElement = $this->domDocument->createElement("value");
            
            $valueElement->appendChild($this->getDomElementFromParameter($subParameter));
            $dataElement->appendChild($valueElement);
        }
        
        $typeElement->appendChild($dataElement);
        
        return $typeElement;
    }
    
}
