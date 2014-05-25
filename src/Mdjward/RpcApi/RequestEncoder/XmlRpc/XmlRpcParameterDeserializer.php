<?php

/**
 * XmlRpcParameterDeserializer.php
 * Definition of class XmlRpcParameterDeserializer
 * 
 * Created 18-May-2014 17:28:19
 *
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 * @copyright (c) 2014, Byng Systems/SkillsWeb Ltd
 */

namespace Mdjward\RpcApi\RequestEncoder\XmlRpc;

use DateTime;
use DOMElement;
use DOMXPath;



/**
 * XmlRpcParameterDeserializer
 * 
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 */
class XmlRpcParameterDeserializer {
    
    protected $forceAssociativeArraysForStructs;
    
    /**
     * 
     * @param boolean $forceAssociativeArraysForStructs
     */
    public function __construct($forceAssociativeArraysForStructs = false) {
        $this->forceAssociativeArraysForStructs = ($forceAssociativeArraysForStructs === true);
    }
    
    /**
     * 
     * @param DOMElement $element
     * @return \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter
     */
    public function getXmlRpcParameterFromDomElement(DOMElement $element) {
        
        $xpath = new DOMXPath($element->ownerDocument);
        
        return new XmlRpcParameter(
            $this->getValueFromDomElement($element),
            $xpath->query("./*", $element)->item(0)->nodeName
        );
    }
    
    /**
     * 
     * @param DOMElement $element
     * @return mixed
     */
    public function getValueFromDomElement(DOMElement $element) {
        
        $xpath = new DOMXPath($element->ownerDocument);
        $actualElement = $xpath->query("./*", $element)->item(0);
        
        switch ($actualElement->nodeName) {
            case XmlRpcParameter::TYPE_STRUCT:
                return $this->getStructValueFromDomElement($actualElement);
                
            case XmlRpcParameter::TYPE_ARRAY:
                return $this->getArrayValueFromDomElement($actualElement);
            
            case XmlRpcParameter::TYPE_DOUBLE:
                return (double) $this->getPrimitiveValueFromDomElement($actualElement);
                
            case XmlRpcParameter::TYPE_INTEGER:
                return (int) $this->getPrimitiveValueFromDomElement($actualElement);
                
            case XmlRpcParameter::TYPE_BOOLEAN:
                return ((int) $this->getPrimitiveValueFromDomElement($actualElement) === 1);
                
            case XmlRpcParameter::TYPE_DATETIME:
                return DateTime::createFromFormat(
                    DateTime::ISO8601,
                    $this->getPrimitiveValueFromDomElement($actualElement)
                );
                
            case XmlRpcParameter::TYPE_STRING:
            case XmlRpcParameter::TYPE_BASE64:
                return $this->getPrimitiveValueFromDomElement($actualElement);
        }
        
        return null;
    }
    
    private function getPrimitiveValueFromDomElement(DOMElement $element) {
        return $element->nodeValue;
    }
    
    private function getStructValueFromDomElement(DOMElement $element) {

        $struct = array();
        $xpath = new DOMXPath($element->ownerDocument);
        
        foreach ($xpath->query("./member", $element) as $memberElement) {
            
            $memberNameElements = $memberElement->getElementsByTagName("name");
            $memberValueElements = $memberElement->getElementsByTagName("value");
            
            if (
                $memberNameElements->length > 0
                && $memberValueElements->length > 0
                && ($actualValueElement = $memberValueElements->item(0)) instanceof DOMElement
            ) {
                $struct[$memberNameElements->item(0)->nodeValue] = $this->getValueFromDomElement(
                    $actualValueElement
                );
            }
        }
        
        return ($this->forceAssociativeArraysForStructs === true ? $struct : (object) $struct);
    }
    
    private function getArrayValueFromDomElement(DOMElement $element) {
        
        $xpath = new DOMXPath($element->ownerDocument);
        $array = array();
        
        
        $dataElements = $xpath->query("./data", $element);
        
        if ($dataElements->length > 0) {
            foreach ($xpath->query("./value", $dataElements->item(0)) as $dataElement) {
                $array[] = $this->getValueFromDomElement($dataElement);
            }
        }
        
        return $array;
    }
    
}
