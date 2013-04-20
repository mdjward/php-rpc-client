<?php

namespace Mattdw\RpcApi\RequestEncoder;

use \Guzzle\Http\Message\Response;



class TransmissionJsonRpcEncoder extends RpcEncoder {
    
    public function encodeRequest($method, array $parameters, $identifier = null) {
        
        $request = array(
            "method"    =>  $method,
            "arguments" =>  $parameters
        );
        
        if ($identifier !== null) {
            $request["tag"] = $identifier;
        }
        
        return json_encode($request);
    }    

    public function decodeResponse(Response $response) {
        return $response->json();
    }
    
}
