<?php

namespace Mattdw\RpcApi\RequestEncoder;

use DateTime;
use DOMDocument;
use DOMText;



/**
 * XmlRpcParameter
 * @author matt
 */
class XmlRpcParameter {
    
    const TYPE_ARRAY = "array";
    const TYPE_BASE64 = "base64";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_DATETIME = "dateTime.iso8601";
    const TYPE_DOUBLE = "double";
    const TYPE_INTEGER = "int";
    const TYPE_STRING = "string";
    const TYPE_STRUCT = "struct";
    const TYPE_NIL = "";
    
    /**
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * 
     * @var string
     */
    private $type;
    
    public function __construct($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function toXml() {
        $dom = $this->toDom();
        $dom->formatOutput = true;
        
        return $dom->saveXml();
    }
    
    public function toDom() {

        $dom = new DOMDocument("1.0", "UTF-8");

        $paramElement = $dom->createElement("param");
        $valueElement = $dom->createElement("value");

        $importedFormattedNode = $dom->importNode($this->formatValue(), true);

        $valueElement->appendChild($importedFormattedNode);
        $paramElement->appendChild($valueElement);

        $dom->appendChild($paramElement);
        
        return $dom;
    }
    
    protected function formatValue() {
        
        switch ($this->type) {
            case self::TYPE_ARRAY:
                return $this->formatArray();
                
            case self::TYPE_STRUCT:
                return $this->formatStruct();
        }
        
        $dom = new DOMDocument("1.0", "UTF-8");
        
        $typeElement = $dom->createElement($this->type);
        $typeElement->appendChild(new DOMText($this->value));
        
        return $typeElement;
    }
    
    private function formatStruct() {
        
        $dom = new DOMDocument("1.0", "UTF-8");
        
        $typeElement = $dom->createElement($this->type);
        
        foreach($this->value as $name => $value) {
            
            $parameter = static::fromValue($value);
            $importedParameterNode = $dom->importNode($parameter->formatValue(), true);
            
            $memberElement = $dom->createElement("member");
            
            $nameElement = $dom->createElement("name");
            $nameElement->appendChild(new DOMText($name));
            
            $valueElement = $dom->createElement("value");
            $valueElement->appendChild($importedParameterNode);
            
            $memberElement->appendChild($valueElement);
            $memberElement->appendChild($nameElement);
            $typeElement->appendChild($memberElement);
        }
        
        return $typeElement;
    }
    
    private function formatArray() {
        
        $dom = new DOMDocument("1.0", "UTF-8");
        
        $typeElement = $dom->createElement($this->type);
        $dataElement = $dom->createElement("data");
        
        foreach ($this->value as $value) {
            
            $parameter = static::fromValue($value);
            $importedParameterNode = $dom->importNode($parameter->formatValue(), true);
            
            $valueElement = $dom->createElement("value");
            
            $valueElement->appendChild($importedParameterNode);
            $dataElement->appendChild($valueElement);
        }
        
        $typeElement->appendChild($dataElement);
        
        return $typeElement;
    }
    
    /**
     * 
     * 
     * @param mixed $value
     *      
     * @return \XmlRpcParameter
     */
    public static function fromValue($value) {
        
        if ($value === null) {
            $type = self::TYPE_NIL;
            
        } else if (is_object($value) || ((is_array($value)) && array_keys($value) !== range(0, count($value) - 1))) {
            $type = self::TYPE_STRUCT;
            
        } else if (is_array($value)) {
            $type = self::TYPE_ARRAY;
            
        } else if (($dateValue = DateTime::createFromFormat(DateTime::ISO8601, $value)) !== false) {
            $value = $dateValue->format(DateTime::ISO8601);
            $type = self::TYPE_DATETIME;
            
        } else if (is_numeric($value)) {
            $doubleValue = (double) $value;
            $integerValue = (int) $value;
            
            if (($doubleValue - $integerValue) == 0) {
                $value = $integerValue;
                $type = self::TYPE_INTEGER;
            } else {
                $value = $doubleValue;
                $type = self::TYPE_DOUBLE;
            }
            
        } else if (is_bool($value) || ($lowerValue = strtolower($value)) == "true" || $lowerValue == "false") {
            $value = (bool) $value;
            $type = self::TYPE_BOOLEAN;
            
        }
        
        return new static($value, (isset($type) ? $type : self::TYPE_STRING));
    }
    
}
