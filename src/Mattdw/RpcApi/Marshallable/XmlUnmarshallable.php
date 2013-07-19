<?php

namespace Mattdw\RpcApi\Marshallable;




interface XmlUnmarshallable {
    
    /**
     * 
     * @return array
     */
    public static function createFromXml($xml);
    
}
