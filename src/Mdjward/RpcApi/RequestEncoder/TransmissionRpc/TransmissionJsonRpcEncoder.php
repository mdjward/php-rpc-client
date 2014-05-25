<?php

namespace Mdjward\RpcApi\RequestEncoder\TransmissionRpc;

use Guzzle\Http\Message\Response;
use Mdjward\RpcApi\RpcErrorException;
use Mdjward\RpcApi\RpcNoResultException;
use Mdjward\RpcApi\RequestEncoder\RpcEncoder;



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
        
        $jsonResponse = $response->json();
        
        if (isset($jsonResponse["result"]) && ($result = $jsonResponse["result"]) !== "success") {
            throw new RpcErrorException($result);
        }
        
        if (!isset($jsonResponse["arguments"])) {
            throw new RpcNoResultException();
        }
        
        return $jsonResponse["arguments"];
    }
    
}
