<?php declare(strict_types=1); namespace IR\SSH; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            SSHKeyAuthentication.php	
 */
    use IR\SSH\SSHAuthentication as SSHAuthentication;
    /**
     * @name            SSHKeyAuthentication.class 
     * @description     It's a class of key authentications types
     * @package		ma\mfw\ssh2
     * @category        SSH
     * @author		Miami Team			
     */
    class SSHKeyAuthentication extends SSHAuthentication 
    {
        /** 
         * @readwrite
         * @access protected 
         * @var string
         */ 
        private $_username;  
        
        /** 
         * @readwrite
         * @access protected 
         * @var string
         */ 
        private $_publicKey; 
        
        /** 
         * @readwrite
         * @access protected 
         * @var string
         */ 
        private $_privateKey;
        
        /**
         * @name __construct
         * @description ssh class constructor
         * @access public
         * @return SSHPasswordAuthentication
         */
        public function __construct($username, $publicKey , $privateKey) 
        {
            $this->_username = $username;
            $this->_publicKey = $publicKey;
            $this->_privateKey = $privateKey;
        }
        
        /**
         * @name getUsername
         * @description get username
         * @access public
         * @return string
         */
        function getUsername() 
        {
            return $this->_username;
        }

        /**
         * @name getUsername
         * @description get public key
         * @access public
         * @return string
         */
        function getPublicKey()
        {
            return $this->_publicKey;
        }

        /**
         * @name getPrivateKey
         * @description get private key
         * @access public
         * @return string
         */
        function getPrivateKey() 
        {
            return $this->_privateKey;
        }

    }  
