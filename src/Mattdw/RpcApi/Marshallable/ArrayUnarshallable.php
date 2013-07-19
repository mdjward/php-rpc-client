<?php

namespace Mattdw\RpcApi\Marshallable;




interface ArrayUnmarshallable {
    
    /**
     * 
     * @return array
     */
    public static function createFromArray(array $array);
    
}
