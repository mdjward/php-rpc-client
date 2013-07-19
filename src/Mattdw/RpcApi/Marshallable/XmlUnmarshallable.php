<?php

namespace Mattdw\RpcApi\Marshallable;




interface XmlUnmarshallable {
    
    /**
     * 
     * @param string $xml
     */
    public static function createFromXml($xml);
    
}
