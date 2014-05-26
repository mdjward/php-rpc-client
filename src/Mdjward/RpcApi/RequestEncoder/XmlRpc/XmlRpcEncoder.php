<?php

namespace Mdjward\RpcApi\RequestEncoder\XmlRpc;

use DOMDocument;
use DOMImplementation;
use DOMNodeList;
use DOMText;
use DOMXPath;
use Guzzle\Http\Message\Response;
use Mdjward\RpcApi\RequestEncoder\RpcEncoder;
use Mdjward\RpcApi\RpcErrorException;
use Mdjward\RpcApi\RpcNoResultException;



/**
 * XmlRpcEncoder
 * @author matt
 */
class XmlRpcEncoder extends RpcEncoder {
    
    /**
     *
     * @var \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterFactory
     * 
     */
    protected $parameterFactory;
    
    /**
     *
     * @var \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterSerializer
     */
    protected $parameterSerializer;
    
    /**
     *
     * @var \Mdjward\RpcApi\RequestEncoder\XmlRpc\XmlRpcParameterDeserializer
     */
    protected $parameterDeserializer;
    
    /**
     *
     * @var \DOMImplementation
     */
    protected $domImplementation;
    
    
    
    public function __construct(
        XmlRpcParameterFactory $parameterFactory,
        XmlRpcParameterSerializer $serializer,
        XmlRpcParameterDeserializer $deserializer,
        DOMImplementation $domImplementation
    ) {
        $this->parameterFactory = $parameterFactory;
        $this->parameterSerializer = $serializer;
        $this->parameterDeserializer = $deserializer;
        $this->domImplementation = $domImplementation;
    }
    
    /**
     * 
     * 
     * @param string $method
     * @param array $parameters
     * @param null $identifier
     * @return type
     */
    public function encodeRequest($method, array $parameters, $identifier = null) {
        
        $dom = $this->buildDocument();
        
        $callElement = $dom->createElement("methodCall");
        
        $methodNameElement = $dom->createElement("methodName");
        $methodNameElement->appendChild(new DOMText($method));
        
        $paramsElement = $dom->createElement("params");
        
        foreach ($parameters as $param) {
            $rpcParameter = $this->parameterFactory->fromValue($param);
            $importedParamNode = $dom->importNode(
                $this->parameterSerializer->getDomElementFromParameter($rpcParameter),
                true
            );
            
            $paramElement = $dom->createElement("param");
            $paramElement->appendChild($importedParamNode);
            
            $paramsElement->appendChild($paramElement);
        }
        
        $callElement->appendChild($methodNameElement);
        $callElement->appendChild($paramsElement);
        $dom->appendChild($callElement);
        
        return $dom->saveXML();
    }
    
    public function decodeResponse(Response $response) {
        
        $rootElement = dom_import_simplexml($response->xml());
        $doc = $rootElement->ownerDocument;
        $xpath = new DOMXPath($doc);
        
        $faultElement = $xpath->query("/methodResponse/fault");
        
        if ($faultElement->length > 0) {
            $errorCodeList = $xpath->query("/methodResponse/fault[1]/value[1]/struct[1]/member[name='faultCode']/value/int");
            $errorMessageList = $xpath->query("/methodResponse/fault[1]/value[1]/struct[1]/member[name='faultString']/value/string");
            
            $errorCode = ($errorCodeList->length > 0 ? (int) $errorCodeList->item(0)->nodeValue : null);
            $errorMessage = ($errorMessageList->length > 0 ? (string) $errorMessageList->item(0)->nodeValue : "");
            
            throw new RpcErrorException($errorMessage, $errorCode);
        }
        
        if (($params = $xpath->evaluate("/methodResponse/params/param")) instanceof DomNodeList && $params->length > 0) {
            $results = array();

            foreach ($params as $paramElement) {
                $results[] = $this->parameterDeserializer->getValueFromDomElement($paramElement);
            }

            return $results;
        }
        
        throw new RpcNoResultException();
    }
    
    /**
     * 
     * @return \DOMDocument
     */
    protected function buildDocument() {
        $dom = $this->domImplementation->createDocument();
        $dom->encoding = "UTF-8";
        $dom->version = "1.0";
        
        return $dom;
    }
    
}
