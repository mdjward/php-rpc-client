<?php

namespace Mattdw\RpcApi;

use Guzzle\Http\Client;
use Mattdw\RpcApi\RequestEncoder\RpcEncoder;



class RequestInitiator {
    
    const NAMESPACE_SEPARATOR = ".";
    
    /**
     *
     * @var int
     */
    protected $requestId = 1;
    
    /**
     *
     * @var \RpcApi\RequestEncoder\RpcEncoder
     */
    protected $encoder;
    
    /**
     *
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $namespace = "";
    
    
    
    public function __construct(RpcEncoder &$encoder, Client $client, $namespace = "") {
        $this->encoder = $encoder;
        $this->client = $client;
        $this->namespace = (string) $namespace;
    }
    
    public function __get($name) {
        return new static($this->encoder, $this->client, $this->createNamespace($name));
    }
    
    public function __call($method, array $parameters) {
        
        // Derive the request body
        $requestBody = $this->encoder->encodeRequest($this->createNamespace($method), $parameters, $this->requestId++);
        
        // Initialise the POST request from the HTTP client
        $request = $this->client->post(null, $this->getRequiredHeaders(), $requestBody);
        
        // Return the decoded response from an invocation of the HTTP request
        return $this->encoder->decodeResponse($request->send());
    }
    
    protected function createNamespace($name) {
        
        $namespace = $this->namespace;
        $namespace .= (!empty($namespace) && !empty($name) ? static::getNamespaceSeparator() : "");
        $namespace .= $name;
        
        return $namespace;
    }
    
    protected static function getNamespaceSeparator() {
        return static::NAMESPACE_SEPARATOR;
    }
    
    protected function getRequiredHeaders() {
        return array();
    }
    
}
