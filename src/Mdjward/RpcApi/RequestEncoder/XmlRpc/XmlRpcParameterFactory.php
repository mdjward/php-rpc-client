<?php

/**
 * XmlRpcParameterFactory.php
 * Definition of class XmlRpcParameterFactory
 * 
 * Created 18-May-2014 17:29:41
 *
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 * @copyright (c) 2014, Byng Systems/SkillsWeb Ltd
 */

namespace Mdjward\RpcApi\RequestEncoder\XmlRpc;

use DateTime;



/**
 * XmlRpcParameterFactory
 * 
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 */
class XmlRpcParameterFactory {
    
    /**
     * 
     * 
     * @param mixed $value
     * @return \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameter
     */
    public function fromValue($value) {
        
        if ($value === null) {
            $type = XmlRpcParameter::TYPE_NIL;
            
        } else if (($dateValue = $value) instanceof DateTime || (is_string($value) && ($dateValue = DateTime::createFromFormat(DateTime::ISO8601, $value)) !== false)) {
            $value = $dateValue->format(DateTime::ISO8601);
            $type = XmlRpcParameter::TYPE_DATETIME;
            
        } else if (is_object($value) || ((is_array($value)) && array_keys($value) !== range(0, count($value) - 1))) {
            $type = XmlRpcParameter::TYPE_STRUCT;
            
        } else if (is_array($value)) {
            $type = XmlRpcParameter::TYPE_ARRAY;
            
        } else if (is_numeric($value)) {
            $doubleValue = (double) $value;
            $integerValue = (int) $value;
            
            if (($doubleValue - $integerValue) == 0) {
                $value = $integerValue;
                $type = XmlRpcParameter::TYPE_INTEGER;
            } else {
                $value = $doubleValue;
                $type = XmlRpcParameter::TYPE_DOUBLE;
            }
            
        } else if (is_bool($value)) {
            $value = $value;
            $type = XmlRpcParameter::TYPE_BOOLEAN;
            
        } else if (($lowerValue = strtolower($value)) === "true" || $lowerValue === "false") {
            
            $value = ($lowerValue === "true");
            $type = XmlRpcParameter::TYPE_BOOLEAN;
        }
        
        return new XmlRpcParameter($value, (isset($type) ? $type : XmlRpcParameter::TYPE_STRING));
    }
    
}
