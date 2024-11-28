<?php declare(strict_types=1); namespace IR\SSH; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            Logger.php	
 */

# utilities
use IR\Utils\Types\Objects as Objects;
use IR\Utils\Types\Strings as Strings;
     # core 
use IR\Core\Application as Application;
use IR\Orm\Database as Database;


# helpers 
use IR\App\Helpers\Page as Page;


# core 
use IR\Core\Base as Base;


    
    use IR\Logs\Logger as Logger;
    use IR\SSH\SSHAuthentication as SSHAuthentication;
    use IR\exceptions\types\BackendException as BackendException;
    /**
     * @name            SSH.class 
     * @description     It's a class that deals with ssh connections
     * @package		ma\mfw\ssh2
     * @category        SSH
     * @author		Miami Team			
     */
    class SSH extends Base
    {       
        /** 
         * @readwrite
         * @access protected 
         * @var resource
         */ 
        protected $_connection;  
        
        /** 
         * @readwrite
         * @access protected 
         * @var resource
         */ 
        protected $_stream;
        
        /** 
         * @readwrite
         * @access protected 
         * @var resource
         */ 
        protected $_sftp;
        
        /** 
         * @readwrite
         * @access protected 
         * @var bool
         */ 
        protected $_isConnected = false;
        
        /** 
         * @readwrite
         * @access protected 
         * @var bool
         */ 
        protected $_isValidLoginType = true;
        
        /** 
         * @readwrite
         * @access protected 
         * @var bool
         */ 
        protected $_isLoggedIn = true;
        
        /**
         * @name __construct
         * @description ssh class constructor
         * @access public
         * @return SSH
         */ 
        public function __construct($hostname,SSHAuthentication $authentication, $port = 22) 
        {
            parent::__construct([]);
            
            $isRunning = $this->isServerRunning($hostname, $port);
            
            if($isRunning == true)
            {
                $this->_connection = ssh2_connect($hostname, $port);
                $auttenticatorClass = Objects::getClassNameWithoutNameSpace($authentication);

                switch($auttenticatorClass) 
                {
                    case 'SSHPasswordAuthentication' :
                    {
                        $username = $authentication->getUsername();
                        $password = $authentication->getPassword();

                        if (ssh2_auth_password($this->getConnection(), $username, $password) === false) 
                        {
                            Logger::getInstance()->error('SSH2 login is invalid');
                            $this->_isConnected = false;
                            $this->_isValidLoginType = true;
                            $this->_isLoggedIn = false;
                        }
                        else
                        {
                            $this->_isConnected = true;
                            $this->_isValidLoginType = true;
                            $this->_isLoggedIn = true;
                        }
                        
                        break;
                    }  
                    case 'SSHKeyAuthentication' :
                    {
                        $username = $authentication->getUsername();
                        $publicKey = $authentication->getPublicKey();
                        $privateKey = $authentication->getPrivateKey();

                        if (ssh2_auth_pubkey_file($this->getConnection(), $username, $publicKey, $privateKey) === false) 
                        {
                            Logger::getInstance()->error('SSH2 login is invalid');
                            $this->_isConnected = false;
                            $this->_isValidLoginType = true;
                            $this->_isLoggedIn = false;
                        }
                        else
                        {
                            $this->_isConnected = true;
                            $this->_isValidLoginType = true;
                            $this->_isLoggedIn = true;
                        }
                        
                        break;
                    }
                    default :
                    {
                        Logger::getInstance()->error('Unknown SSH2 login type');
                        $this->_isConnected = false;
                        $this->_isValidLoginType = false;
                        $this->_isLoggedIn = false;
                    } 
                }
            }   
        } 
        
        /**
         * @name disconnect
         * @description disconnect from a server
         * @access public
         * @return boolean
         */
        public function disconnect() 
        {
            if ($this->getConnection()) 
            {
                $this->cmd("exit");
                unset($this->_connection);
                unset($this->_stream);
                return true;
            }
            return false;
        }
        
        /**
         * @name isConnected
         * @description get connecteion status
         * @access public
         * @return boolean
         */
        public function isConnected() 
        {
            return $this->_isConnected && $this->_isValidLoginType && $this->_isLoggedIn;
        }
        
        /**
         * @name isServerRunning
         * @description check if server is running
         * @access public
         * @return boolean
         */
        public function isServerRunning($hostname,$port) 
        { 
            $running = false;
            
            if($fp = fsockopen($hostname,$port,$errCode,$errStr,5))
            {   
                $running = true;
            } 

            fclose($fp);
            
            return $running;
        }


        /**
         * @name connection
         * @description check if server is running
         * @access public
         * @return boolean
         */
        public function connection() 
        { 
            return $this->_connection;
        }
        
        /**
         * @name cmd
         * @description execute a command remotely
         * @access public
         * @param string $command
         * @param boolean $showOutput
         * @return mixed
         */
        public function cmd($command,$showOutput = false,$liveOutput = false) 
        {
            $this->_stream = ssh2_exec($this->getConnection(),$command);
            stream_set_blocking($this->_stream, true);
            
            # output
            if($showOutput == true)
            {
                if($liveOutput == true)
                {
                    while ($line = fgets($this->_stream)) 
                    {
                        echo $line;
                    }
                    
                    return true;
                }
                else
                {
                    $output = '';
                
                    while ($line = fgets($this->_stream)) 
                    {
                           $output .= $line;
                    }

                   return $output;
                }
            }
            
            return true;
        }
        
        /**
         * @name sftp
         * @description this method is to call sftp functions
         * @access public
         * @return mixed
         */
        public function sftp($function,$parameters) 
        {
            $this->_sftp = ssh2_ftp($this->getConnection());
            
            $function = 'ssh2_sftp_' . $function;
            
            if (function_exists($function)) 
            {
                array_unshift($parameters,$this->getConnection());
                return call_user_func_array($function, $parameters);
            } 
            else 
            {
                throw new BackendException($function . ' is not a valid SFTP function');
            }
        }
        
        /**
         * @name scp
         * @description this method is to call scp functions
         * @access public
         * @return mixed
         */
        public function scp($function, $parameters , $content = null) 
        {
            $function = 'ssh2_scp_' . $function;
            
            if (function_exists($function) && count($parameters) > 0) 
            {
                # send a content instead of file
                if($content != null && strlen($content) > 0)
                {
                    $fileName = ROOT_PATH . DS . DEFAULT_TEMP_DIRECTORY . DS . Strings::generateRandomText(10,true,true,true,false);
                    file_put_contents($fileName,$content);
                    $parameters = array($fileName,$parameters[0]);
                }
                
                array_unshift($parameters,$this->getConnection());
                $result = call_user_func_array($function, $parameters);
                
                if($content != null && strlen($content) > 0)
                {
                    unlink($fileName);
                }
                
                return $result;
            } 
            else 
            {
                throw new BackendException($function . ' is not a valid SCP function');
            }
        }
    }  
