<?php

namespace Mattdw\RpcApi\RequestEncoder;

use \Guzzle\Http\Message\Response;



class JsonRpcV1Encoder extends AbstractJsonRpcEncoder {

    protected function produceJsonEncodeableArray($method, array $parameters, $identifier = null) {
        return parent::produceJsonEncodeableArray(
            $method,
            array_values($parameters),
            $identifier
        );
    }
    
}

