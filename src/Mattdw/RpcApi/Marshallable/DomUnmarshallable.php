<?php

namespace Mattdw\RpcApi\Marshallable;



interface DomUnmarshallable {
    
    /**
     * 
     * @return array
     */
    public static function createFromDom(\DOMElement $element);
    
}
