<?php

namespace Mattdw\RpcApi\RequestEncoder;

use Guzzle\Http\Message\Response;



/**
 * AbstractJsonRpcEncoder
 * @author matt
 */
abstract class AbstractJsonRpcEncoder extends RpcEncoder {
    
    public function decodeResponse(Response $response) {
        return $response->json();
    }
    
    protected function produceJsonEncodeableArray($method, array $parameters, $identifier = null) {
        
        $request = array(
            "method"        =>  $method,
            "params"        =>  $parameters
        );
        
        if ($identifier !== null) {
            $request["id"] = $identifier;
        }
        
        return $request;
    }

    public function encodeRequest($method, array $parameters, $identifier = null) {
        return json_encode($this->produceJsonEncodeableArray($method, $parameters, $identifier));
    }    
    
}
