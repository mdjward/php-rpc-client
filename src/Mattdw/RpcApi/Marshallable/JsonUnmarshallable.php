<?php

namespace Mattdw\RpcApi\Marshallable;




interface JsonUnmarshallable {
    
    /**
     * 
     * @param string $json
     */
    public static function createFromJson($json);
    
}
