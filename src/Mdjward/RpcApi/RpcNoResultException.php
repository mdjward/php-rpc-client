<?php

/**
 * RpcNoResultException.php
 * Definition of class RpcNoResultException
 * 
 * Created 18-May-2014 16:55:58
 *
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 * @copyright (c) 2014, Byng Systems/SkillsWeb Ltd
 */

namespace Mdjward\RpcApi;

/**
 * RpcNoResultException
 * 
 * @author M.D.Ward <matthew.ward@byng-systems.com>
 */
class RpcNoResultException extends RpcException {
    
    /**
     * 
     */
    const EXCEPTION_MESSAGE = "No response given";
    
    
    
    /**
     * 
     */
    public function __construct() {
        parent::__construct(self::EXCEPTION_MESSAGE, null, null);
    }
    
}

