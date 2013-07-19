<?php

namespace Mattdw\RpcApi\Marshallable;




interface DomMarshallable {
    
    /**
     * 
     * @return \DOMElement
     */
    public function toDom();
    
}
