<?php declare(strict_types=1); namespace IR\SSH; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            Logger.php	
 */
    use IR\SSH\SSHAuthentication as SSHAuthentication;
    /**
     * @name            SSHPasswordAuthentication.class 
     * @description     It's a class of username/password authentications types
     * @package		ma\mfw\ssh2
     * @category        SSH
     * @author		Miami Team			
     */
    class SSHPasswordAuthentication extends SSHAuthentication
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
        private $_password; 
        
        /**
         * @name __construct
         * @description ssh class constructor
         * @access public
         * @return SSHPasswordAuthentication
         */
        public function __construct($username, $password) 
        {
            $this->_username = $username;
            $this->_password = $password;
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
         * @name getPassword
         * @description get get password
         * @access public
         * @return string
         */
        function getPassword() 
        {
            return $this->_password;
        }
    }  
