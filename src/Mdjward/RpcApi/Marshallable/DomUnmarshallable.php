<?php

namespace Mdjward\RpcApi\Marshallable;



interface DomUnmarshallable {
    
    /**
     * 
     * @param \DOMElement $element
     */
    public static function createFromDom(\DOMElement $element);
    
}
