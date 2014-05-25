<?php

namespace Mdjward\RpcApi\Marshallable;




interface DomMarshallable {
    
    /**
     * 
     * @return \DOMElement
     */
    public function toDom();
    
}
