<?php

namespace Mdjward\RpcApi\Marshallable;




interface JsonUnmarshallable {
    
    /**
     * 
     * @param string $json
     */
    public static function createFromJson($json);
    
}
