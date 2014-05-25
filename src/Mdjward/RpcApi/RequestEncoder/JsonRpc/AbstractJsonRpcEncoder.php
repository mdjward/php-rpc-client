<?php

namespace Mdjward\RpcApi\RequestEncoder\JsonRpc;

use Guzzle\Http\Message\Response;
use Mdjward\RpcApi\RpcErrorException;
use Mdjward\RpcApi\RpcNoResultException;
use Mdjward\RpcApi\RequestEncoder\RpcEncoder;



/**
 * AbstractJsonRpcEncoder
 * @author matt
 */
abstract class AbstractJsonRpcEncoder extends RpcEncoder {
    
    public function decodeResponse(Response $response) {
        $json = $response->json();
        
        if (isset($json["error"])) {
            throw new RpcErrorException($json["error"]);
        }
        
        if (!isset($json["result"])) {
            throw new RpcNoResultException();
        }
        
        return $json["result"];
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
