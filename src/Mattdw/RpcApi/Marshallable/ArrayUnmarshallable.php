<?php

namespace Mattdw\RpcApi\Marshallable;




interface ArrayUnmarshallable {
    
    /**
     * 
     * @param array $array
     */
    public static function createFromArray(array $array);
    
}
