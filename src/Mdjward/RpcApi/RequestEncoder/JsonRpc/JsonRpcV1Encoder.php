<?php

namespace Mdjward\RpcApi\RequestEncoder\JsonRpc;



class JsonRpcV1Encoder extends AbstractJsonRpcEncoder {

    protected function produceJsonEncodeableArray($method, array $parameters, $identifier = null) {
        return parent::produceJsonEncodeableArray(
            $method,
            array_values($parameters),
            $identifier
        );
    }
    
}
