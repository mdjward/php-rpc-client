<?php

namespace Mattdw\RpcApi\Marshallable;




interface JsonUnmarshallable {
    
    /**
     * 
     * @return array
     */
    public static function createFromJson($json);
    
}
