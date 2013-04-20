<?php

namespace Mattdw\RpcApi\RequestEncoder;

use Guzzle\Http\Message\Response;



/**
 * Encoder
 * @author matt
 */
abstract class RpcEncoder {
    
    public abstract function encodeRequest($method, array $parameters, $identifier = null);
    
    public abstract function decodeResponse(Response $response);
    
}

