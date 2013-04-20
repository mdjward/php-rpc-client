<?php

namespace Mattdw\RpcApi\RequestEncoder;

use Guzzle\Http\Message\Response;



/**
 * JsonRpcV2Encoder
 * @author matt
 */
class JsonRpcV2Encoder extends AbstractJsonRpcEncoder {
    
    protected function produceJsonEncodeableArray($method, array $parameters, $identifier = null) {
        
        $request = parent::produceJsonEncodeableArray($method, $parameters, $identifier);
        $request["jsonrpc"] = "2.0";
        
        return $request;
    }
    
}
