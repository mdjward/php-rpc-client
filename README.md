php-rpc-client
==============

Web Service RPC client implementation for PHP.

This is a project I worked on a while back, so there are probably much better implementations out there at present.

The library aims to provide a simple, transparent means of working with RPC-based web services, using [Guzzle](http://guzzle.readthedocs.org/en/latest/) as the underlying HTTP client library.  At present, it supports:
1. [JSON-RPC](http://json-rpc.org/) (versions 1.0 and 2.0);
2. [XML-RPC](http://xmlrpc.scripting.com/default.html);
3. The RPC format used by the [Transmission](https://www.transmissionbt.com/) BitTorrent engine.

Making a request
----------------

Generally this library takes advantage of magic methods in PHP (specifically __get and __call), in much the same way as the PHP SOAP client to invoke web service methods under multiple levels of namespace.

### Example: Updating XBMC's media library

The popular [XBMC media centre](http://xbmc.org/) makes use of JSON-RPC v2 in the [extensive web service](http://wiki.xbmc.org/?title=JSON-RPC_API/v6) it offers for remote control purposes.

To invoke it's [VideoLibrary.Scan](http://wiki.xbmc.org/?title=JSON-RPC_API/v6#VideoLibrary.Scan) method with an empty string parameter, on a server running on :

```php
<?php

use Guzzle\Http\Client;
use Mdjward\RpcApi\RequestInitiator;
use Mdjward\RpcApi\RequestEncoder\JsonRpc\JsonRpcV2Encoder;

// Initialise the Guzzle client to the JSON RPC endpoint offered by XBMC
$client = new Client("http://xbmc.myhomenetwork.org:8080/jsonrpc");

// Initialise an object of the prescribed class for encoding/decoding JSON RPC v2 messages
$encoder = new JsonRpcV2Encoder();

// Initialise the initiator!
$initiator = new RequestInitiator($client, $encoder);

// Use a 
$response = $initiator->VideoLibrary->Scan("");

```

This should produce a ```string``` "OK", as the principle result of the method.

However, since magic method resolution is well known to have extremely poor performance, you are advised to extend the RequestInitiator class for purpose-specific APIs and create purpose-specific methods (and, potentially, fields/properties), as in the example below (although probably not with a public property, and possibly with dependency injection as opposed to tight coupling).

```php
<?php

// ...

class XbmcRequestInitiator extends RequestInitiator {
    
    public $videoLibrary;
    
    public function __construct(Client $client, RpcEncoder $encoder) {
        parent::__construct($client, $encoder, "");
        
        $this->videoLibrary = new VideoLibraryRequestInitiator($client, $encoder);
    }
    
}

// ...

class VideoLibraryRequestInitiator extends RequestInitiator {
    
    public function __construct() {
        parent::__construct($client, $encoder, "VideoLibrary");
    }
    
    public function scan($directory = "") {
        return $this->__call("Scan", array("directory" => $directory);
    }
    
}

// ...

// Initialise the XBMC initiator!
$initiator = new XbmcRequestInitiator($client, $encoder);

// Use
$response = $initiator->videoLibrary->scan("");


```

In this way, it is possible to build an entire client library for the XBMC RPC API or any supported RPC API.
