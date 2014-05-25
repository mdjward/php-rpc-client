<?php

namespace Mdjward\RpcApi\Marshallable;




interface ArrayUnmarshallable {
    
    /**
     * 
     * @param array $array
     */
    public static function createFromArray(array $array);
    
}
