<?php

namespace Mdjward\RpcApi\RequestEncoder\XmlRpc;

use InvalidArgumentException;



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
    
    /**
     * 
     * @param mixed $value
     * @param string $type
     * @throws \InvalidArgumentException
     */
    public function __construct($value, $type) {
        $this->value = $value;
        $this->type = self::validateType($type);
    }
    
    /**
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * 
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected static function validateType($type) {
        
        if (!is_string($type)) {
            throw new InvalidArgumentException("Type must be given as a string only");
        }
        
        switch ($type) {
            case self::TYPE_ARRAY:
            case self::TYPE_BASE64:
            case self::TYPE_BOOLEAN:
            case self::TYPE_DATETIME:
            case self::TYPE_DOUBLE:
            case self::TYPE_INTEGER:
            case self::TYPE_NIL:
            case self::TYPE_STRING:
            case self::TYPE_STRUCT:
                return $type;
        }
        
        throw new InvalidArgumentException("Invalid type '{$type}'");
    }
    
}
