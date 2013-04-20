<?php

namespace Mattdw\RpcApi\RequestEncoder;

use DOMDocument;
use DOMText;
use \Guzzle\Http\Message\Response;



/**
 * XmlRpcEncoder
 * @author matt
 */
class XmlRpcEncoder extends RpcEncoder {
    
    public function encodeRequest($method, array $parameters, $identifier = null) {
        
        $dom = new DOMDocument("1.0", "UTF-8");
        
        $callElement = $dom->createElement("methodCall");
        
        $methodNameElement = $dom->createElement("methodName");
        $methodNameElement->appendChild(new DOMText($method));
        
        $paramsElement = $dom->createElement("params");
        
        foreach ($parameters as $param) {
            $rpcParameter = XmlRpcParameter::fromValue($param);
            $importedParamNode = $dom->importNode($rpcParameter->toDom()->documentElement, true);
            
            $paramsElement->appendChild($importedParamNode);
        }
        
        $callElement->appendChild($methodNameElement);
        $callElement->appendChild($paramsElement);
        $dom->appendChild($callElement);
        
        return $dom->saveXML();
    }
    
    public function decodeResponse(Response $response) {
        
        
        
    }
    
}

