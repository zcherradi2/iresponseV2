<?php declare(strict_types=1); namespace IR\App\Webservices; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            Pmta.php	
 */

# core 
use IR\Core\Base as Base;
use IR\Core\Application as Application;

# helpers 
use IR\App\Helpers\Authentication as Authentication;
use IR\App\Helpers\Permissions as Permissions;
use IR\App\Helpers\Page as Page;
use IR\App\Helpers\Api as Api;

# orm 
use IR\Orm\Table as Table;

# models
use IR\App\Models\Admin\MtaServer as MtaServer;
use IR\App\Models\Admin\ServerVmta as ServerVmta;
use IR\App\Models\Admin\PmtaProcess as PmtaProcess;

use IR\SSH\SSHPasswordAuthentication as SSHPasswordAuthentication;
use IR\SSH\SSH as SSH;
/**
 * @name Pmta
 * @description Pmta WebService
 */
class Pmta extends Base
{
    /**
     * @app
     * @readwrite
     */
    protected $app;

    /**
     * @name init
     * @description initializing process before the action method executed
     * @once
     * @protected
     */
    public function init() 
    {
        # set the current application to a local variable
        $this->app = Application::getCurrent();
    }

  /**
     * @name updateRootVmtas
     * @description updateRootVmtas action
     * @before init
     */
    public function updateRootVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','individualVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }

        $username = Authentication::getAuthenticatedUser()->getEmail();
        
        $ipsDomains = array_filter(explode(PHP_EOL,strval($this->app->utils->arrays->get($parameters,'mapping',''))));
        $serverid = intval($this->app->utils->arrays->get($parameters,'servers'));

        
        
        if(count($ipsDomains) > 0)
        {
            if($serverid == null || $serverid == 0)
            {
                Page::printApiResults(500,'server not selected !');
            }
            
            $pairs = [];
            $mapping = [];
            $ipsFromMap = [];
            
            
                    
                    
                    $vmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['status = ? and type = ? and mta_server_id = ?',['Activated','Default',$serverid]]);
                    $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverid]]);

                    

                    $sshAuthenticator = new  SSHPasswordAuthentication('root',$mtaServer['ssh_password']);
                    $sshConnector = new SSH($mtaServer['main_ip'],$sshAuthenticator,22);
                    if($sshConnector->isConnected())
                    {
                        foreach ($vmtas as  $vmta) {


                            foreach ($ipsDomains as $row) 
            {
                if($this->app->utils->strings->contains($row,'|'))
                {
                    $domain = trim(strval(explode('|',$row)[0]));
                    $root = trim(strval(explode('|',$row)[1])); 
                    $vmta_root = file_get_contents(ASSETS_PATH . DS . 'templates/servers' . DS . 'vmta-root.tpl');

                            $countvmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['status = ?  and ip = ?',['Activated',$vmta['ip']]]);

                            $newvmta = new ServerVmta();
                            $newvmta->setMtaServerId($serverid);
                            $newvmta->setMtaServerName($mtaServer['name']);
                            $newvmta->setIspId(0);
                            $newvmta->setStatus('Activated');
                            $newvmta->setType('Custom');
                            $newvmta->setName(str_replace('.','_',$vmta['ip']).'_'.(count($countvmtas)+1));
                            $newvmta->setDomain($domain);
                            $newvmta->setCustomDomain($domain); 
                            $newvmta->setIp($vmta['ip']);
                            $newvmta->setCreatedBy($username);
                            $newvmta->setCreatedDate(date('Y-m-d'));
                            $newvmta->setLastUpdatedBy($username);
                            $newvmta->setLastUpdatedDate(date('Y-m-d'));
                            $idres = $newvmta->insert();

                            $namevmta = str_replace('.','_',$vmta['ip']) .'_'.(count($countvmtas)+1);
                            
                            
                            $vmta_root = str_replace('$P{ROOT}',$root,$vmta_root);
                            $vmta_root = str_replace('$P{IP}',$vmta['ip'],$vmta_root);
                            $vmta_root = str_replace('$P{DOMAIN}',$domain,$vmta_root);
                            $vmta_root = str_replace('$P{VMTA}',$namevmta,$vmta_root);

                            

                            $sshConnector->cmd("echo '".$vmta_root."' > /etc/pmta/vmtas/".$namevmta.".conf");
                            $sshConnector->cmd("service pmta restart"); 
                        }
                    }

                        }
                    }
                    else{
                        Page::printApiResults(500,' server not connected !');
                    }
             
            
            


            
            
            
            
            Page::printApiResults(200,'successfull add vmta');
        }
        else
        {
            Page::printApiResults(500,'No ips / domains found !');
        }
    }


 /**
     * @name resetIndividualVmtas
     * @description resetIndividualVmtas action
     * @before init
     */
    public function resetRootVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','individualVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverid = intval($this->app->utils->arrays->get($parameters,'seerver-id'));

        
        $vmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['status = ? and type = ? and mta_server_id = ?',['Activated','Custom',$serverid]]);
        $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverid]]);

        

        $sshAuthenticator = new  SSHPasswordAuthentication('root',$mtaServer['ssh_password']);
        $sshConnector = new SSH($mtaServer['main_ip'],$sshAuthenticator,22);
        if($sshConnector->isConnected())
        {
            foreach ($vmtas as  $vmta) {

                $countvmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['status = ?  and ip = ?',['Activated',$vmta['ip']]]);

                $newvmta = new ServerVmta(array('id' => $vmta['id']));
                $idres = $newvmta->delete();

                $sshConnector->cmd("rm -rf  /etc/pmta/vmtas/".$vmta['name'].".conf"); 

            }

            
            Page::printApiResults(200,"successfull reset Vmtas");
        }
        else{
            Page::printApiResults(500,"server can't connected");
        }
        
    }
    
    /**
     * @name getServerVmtas
     * @description getServerVmtas action
     * @before init
     */
    public function getServerVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','configs')
                || Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','commands')
                || Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','templates');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverIds = array_filter($this->app->utils->arrays->get($parameters,'server-ids'));

        if(count($serverIds))
        {
            $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id in ?',['Activated',$serverIds]]);

            if(count($mtaServer) == 0)
            {
                Page::printApiResults(404,'Mta server not found !');
            }

            $result = ServerVmta::all(ServerVmta::FETCH_ARRAY,['status = ? and mta_server_id in ?',['Activated',$serverIds]],['id','mta_server_id','mta_server_name','name','ip','domain']);
            
            $vmtas = [];

            if(count($result))
            {
                foreach ($result as $row) 
                {
                    $vmtas[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'ip' => $row['ip'],
                        'rdns' => $row['domain'],
                        'domain' => $this->app->utils->domains->getDomainFromURL($row['domain']),
                        'server-id' => $row['mta_server_id'],
                        'server-name' => $row['mta_server_name']
                    ];
                }

                if(count($vmtas))
                {
                    Page::printApiResults(200,'',['vmtas' => $vmtas]);
                }
                else
                {
                    Page::printApiResults(500,'No vmtas Found !');
                }
            }
            else
            {
                Page::printApiResults(500,'Error while trying to get vmtas !');
            }
        }
        else
        {
            Page::printApiResults(500,'Incorrect server Id !');
        }
    }
    
    /**
     * @name getTemplateConfig
     * @description getConfig action
     * @before init
     */
    public function getTemplateConfig($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','templates');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }

        $type = $this->app->utils->arrays->get($parameters,'type');

        if($type == null || $type == '')
        {
            Page::printApiResults(404,'Config type not found !');
        }
        
        $name = $this->app->utils->arrays->get($parameters,'name');

        if($name == null || $name == '')
        {
            Page::printApiResults(404,'Config not found !');
        }

        $config = "";
        $pmtaConfigFolder = ASSETS_PATH . DS . 'pmta' . DS . 'configs' . DS .  $type;
      
        if(file_exists($pmtaConfigFolder . DS . 'parameters' . DS . $name . '.conf'))
        {
            $config = $this->app->utils->fileSystem->readFile($pmtaConfigFolder . DS . 'parameters' . DS . $name . '.conf');
        }
        else
        {
            Page::printApiResults(404,'Config not found !');
        }
        
        Page::printApiResults(200,'',['config' => $config]);
    }
    
    /**
     * @name saveTemplateConfig
     * @description saveTemplateConfig action
     * @before init
     */
    public function saveTemplateConfig($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','templates');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }

        $serverIds = $this->app->utils->arrays->get($parameters,'servers-ids',[]);
        $name = $this->app->utils->arrays->get($parameters,'name');

        if($name == null || $name == '')
        {
            Page::printApiResults(404,'Config not found !');
        }

        $content = $this->app->utils->arrays->get($parameters,'content');

        if($content == null || $content == '')
        {
            Page::printApiResults(404,'Config content found !');
        }

        $type = $this->app->utils->arrays->get($parameters,'type');

        if($type == null || $type == '')
        {
            Page::printApiResults(404,'Config type not found !');
        }
        
        $pmtaConfigFolder = ASSETS_PATH . DS . 'pmta' . DS . 'configs' . DS .  $type;

        if(file_exists($pmtaConfigFolder . DS . 'parameters' . DS . $name . '.conf'))
        {
            $this->app->utils->fileSystem->writeFile($pmtaConfigFolder . DS . 'parameters' . DS . $name . '.conf',$content);
            
            # apply on active servers if any
            if(is_array($serverIds) && count($serverIds))
            {
                # call iresponse api
                $result = Api::call('Pmta','applyTemplateConfigs',['servers-ids' => $serverIds,'path' => '/etc/pmta/parameters/' . $name . '.conf','content' => base64_encode($content)]);

                if(count($result) == 0)
                {
                    Page::printApiResults(500,'No response found !');
                }

                if($result['httpStatus'] == 500)
                {
                    Page::printApiResults(500,$result['message']);
                }
            }
            
            Page::printApiResults(200,'Config file updated successfully !');
        }
        else
        {
            Page::printApiResults(404,'Config not found !');
        }
    }
    
    /**
     * @name getConfig
     * @description getConfig action
     * @before init
     */
    public function getConfig($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','configs');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverId = intval($this->app->utils->arrays->get($parameters,'server-id'));

        if($serverId > 0)
        {
            $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverId]]);

            if(count($mtaServer) == 0)
            {
                Page::printApiResults(404,'Mta server not found !');
            }

            $type = $this->app->utils->arrays->get($parameters,'type');

            if($type == null || $type == '')
            {
                Page::printApiResults(404,'Config type not found !');
            }

            $name = $this->app->utils->arrays->get($parameters,'name');

            if($name == null || $name == '')
            {
                Page::printApiResults(404,'Config not found !');
            }

            # call iresponse api
            $result = Api::call('Pmta','getConfig',['server-id' => $serverId,'type' => $type,'name' => $name]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,'',['config' => $result['data']]);
        }
        else
        {
            Page::printApiResults(500,'Incorrect server id !');
        }
    }



    /**
     * @name checkVersion
     * @description checkVersion action
     * @before init
     */
    public function checkVersion($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','configs');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverId = intval($this->app->utils->arrays->get($parameters,'server-id'));
        
        $result = '';

        if($serverId > 0)
        {
            $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverId]]);

            if(count($mtaServer) == 0)
            {
                Page::printApiResults(404,'Mta Server not found !');
            }


            $sshAuthenticator = new  SSHPasswordAuthentication('root',$mtaServer['ssh_password']);
            $sshConnector = new SSH($mtaServer['main_ip'],$sshAuthenticator,22);
            if($sshConnector->isConnected())
            {
                $result = $sshConnector->cmd('pmtad --version;',true);
            }

            Page::printApiResults(200,$result . ASSETS_PATH);
        }
        else
        {
            Page::printApiResults(500,'Incorrect server id !');
        }
    }


    /**
     * @name installNewPmta
     * @description installNewPmta action
     * @before init
     */
    public function installNewPmta($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','configs');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverId = intval($this->app->utils->arrays->get($parameters,'server-id'));
        $version = $this->app->utils->arrays->get($parameters,'pmta-v');
        $result = 'Start Upload pmta\r\n';
        $pmta_host = file_get_contents(ASSETS_PATH . DS . 'templates/servers' . DS . 'pmta-host.tpl');
        $pmtaPort = $this->app->utils->arrays->get($this->app->getSetting('application'),'pmta_http_port');

        if($serverId > 0)
        {
            $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverId]]);

            if(count($mtaServer) == 0)
            {
                Page::printApiResults(404,'Mta Server not found !');
            }


            $sshAuthenticator = new  SSHPasswordAuthentication('root',$mtaServer['ssh_password']);
            $sshConnector = new SSH($mtaServer['main_ip'],$sshAuthenticator,22);

            if($sshConnector->isConnected())
            {
                $release = $sshConnector->cmd("cat /etc/*release",true). PHP_EOL;
                $dataos = $this->parseTexOSt($release);

                if($version == 'v5'){
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $sshConnector->cmd("rm -rf /etc/httpd/conf.d/powermta.conf"); 
                        $sshConnector->cmd("rm -rf /root/PowerMTA-5.0r3.rpm'");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta5' . DS . 'PowerMTA-5.0r3.rpm','/root/PowerMTA-5.0r3.rpm'));
                        $pmta_host = str_replace('$_PORT',$pmtaPort,$pmta_host); 
                        $sshConnector->cmd("touch  /etc/httpd/conf.d/powermta.conf"); 
                        $sshConnector->cmd("echo '".$pmta_host."' >> /etc/httpd/conf.d/powermta.conf"); 
                    }else{
                        $sshConnector->cmd("rm -rf /etc/apache2/sites-available/powermta.conf"); 
                        $sshConnector->cmd("rm -rf /root/powermta_amd64.deb");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta5' . DS . 'powermta_amd64.deb','/root/powermta_amd64.deb'));
                        $pmta_host = str_replace('$_PORT',$pmtaPort,$pmta_host); 
                        $sshConnector->cmd("touch   /etc/apache2/sites-available/powermta.conf"); 
                        $sshConnector->cmd("echo '".$pmta_host."' >> /etc/apache2/sites-available/powermta.conf");
                    }
                    
            
                }
                elseif ($version == 'v45') {
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $sshConnector->cmd("rm -rf /etc/httpd/conf.d/powermta.conf"); 
                        $sshConnector->cmd("rm -rf /root/PowerMTA-4.5r8.rpm'");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta45' . DS . 'PowerMTA-4.5r8.rpm','/root/PowerMTA-4.5r8.rpm'));
                        $pmta_host = str_replace('$_PORT',$pmtaPort,$pmta_host); 
                        $sshConnector->cmd("touch  /etc/httpd/conf.d/powermta.conf"); 
                        $sshConnector->cmd("echo '".$pmta_host."' >> /etc/httpd/conf.d/powermta.conf"); 
                    }else{
                         
                        $sshConnector->cmd("rm -rf /etc/apache2/sites-available/powermta.conf"); 
                        $sshConnector->cmd("rm -rf /root/powermta_amd64.deb");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta45' . DS . 'powermta_amd64.deb','/root/powermta_amd64.deb'));
                        $pmta_host = str_replace('$_PORT',$pmtaPort,$pmta_host); 
                        $sshConnector->cmd("touch   /etc/apache2/sites-available/powermta.conf"); 
                        $sshConnector->cmd("echo '".$pmta_host."' >> /etc/apache2/sites-available/powermta.conf");
                    }
                }
                else{
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false  || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $sshConnector->cmd("rm -rf /root/pmta64.rpm'");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/rpms' . DS . 'pmta64.rpm','/root/pmta64.rpm'));
                        $sshConnector->cmd("rm -rf /etc/httpd/conf.d/powermta.conf");  
                        $sshConnector->cmd("systemctl restart httpd");
                    }
                    else{
                        $sshConnector->cmd("rm -rf /root/powermta_amd64.deb");
                        $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/rpms' . DS . 'powermta_amd64.deb','/root/powermta_amd64.deb'));
                        $sshConnector->cmd("a2dissite powermta"); 
                        $sshConnector->cmd("rm -rf /etc/apache2/sites-available/powermta.conf");  
                        $sshConnector->cmd("systemctl reload apache2"); 
                        $sshConnector->cmd("systemctl restart apache2"); 
                    }
                }
                
                $result .= $sshConnector->cmd("cp /etc/pmta/config /home",true) . "\r\n";
                $result .= $sshConnector->cmd("cp -R /etc/pmta/parameters /home/",true) . "\r\n";  
                $result .= $sshConnector->cmd("cp -R /etc/pmta/keys /home/",true) . "\r\n"; 
                $result .= $sshConnector->cmd("cp -R /etc/pmta/bounces /home/",true) . "\r\n"; 
                $result .= $sshConnector->cmd("cp -R /etc/pmta/delivered /home/",true) . "\r\n"; 
                $result .= $sshConnector->cmd("cp -R /etc/pmta/deffered /home/",true) . "\r\n"; 
                $result .= $sshConnector->cmd("cp -R /etc/pmta/vmtas /home",true) . "\r\n";
                $result .= $sshConnector->cmd("systemctl disable pmta ",true) . "\r\n";
                $result .= $sshConnector->cmd("service pmta stop && service pmtahttp stop;",true) . "\r\n";
                if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                    $result .= $sshConnector->cmd("rpm -e $(rpm -qa 'PowerMTA*');",true) . "\r\n";
                }else{
                    $result .= $sshConnector->cmd("dpkg -r $(dpkg -f /root/powermta_amd64.deb Package);",true) . "\r\n";
                }
                $sshConnector->cmd('rm -rf /etc/pmta;');
                $sshConnector->cmd('rm -rf /usr/sbin/pmtad;');
                $sshConnector->cmd('rm -rf /usr/sbin/pmtahttpd;');
                $sshConnector->cmd('rm -rf /usr/sbin/pmta*;');
                $sshConnector->cmd('systemctl daemon-reload;'); 
                if($version == 'v5'){
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $result .= $sshConnector->cmd("rpm -Uvh /root/PowerMTA-5.0r3.rpm;",true) . "\r\n"; 
                    }
                    else{
                        $result .= $sshConnector->cmd("yes | dpkg -i /root/powermta_amd64.deb;",true) . "\r\n"; 
                    }
                    $sshConnector->cmd("rm -rf /etc/pmta/config-defaults;");
                    $sshConnector->cmd("rm -rf /etc/pmta/licens*;");
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta5' . DS . 'license','/etc/pmta/license'));  
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta5/usr/sbin' . DS . 'pmtad','/usr/sbin/pmtad'));
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta5/usr/sbin' . DS . 'pmtahttpd','/usr/sbin/pmtahttpd'));

                }
                elseif ($version == 'v45') {
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $result .= $sshConnector->cmd("rpm -Uvh /root/PowerMTA-4.5r8.rpm;",true) . "\r\n"; 
                    }
                    else{
                        $result .= $sshConnector->cmd("yes | dpkg -i /root/powermta_amd64.deb;",true) . "\r\n"; 
                    }
                    $sshConnector->cmd("rm -rf /etc/pmta/config-defaults;");
                    $sshConnector->cmd("rm -rf /etc/pmta/licens*;");
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta45' . DS . 'license','/etc/pmta/license'));  
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/pmta45' . DS . 'pmtad','/usr/sbin/pmtad'));
                }
                else{
                    if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                        $result .= $sshConnector->cmd("rpm -Uvh /root/pmta64.rpm;",true) . "\r\n"; 
                    }else{
                        $result .= $sshConnector->cmd("yes | dpkg -i /root/powermta_amd64.deb;",true) . "\r\n"; 
                    }
                    $sshConnector->cmd("rm -rf /etc/pmta/*");
                    $sshConnector->cmd("rm -rf /etc/pmta/license-notice;");
                    $sshConnector->cmd("rm -rf /etc/pmta/license;");
                    $sshConnector->scp('send',array(ASSETS_PATH . DS . 'pmta/configs/license','/etc/pmta/license')); 
                }

                //$sshConnector->cmd("unzip  /root/pmta5.zip;") . "\r\n";
                
                $sshConnector->cmd("mv  /home/parameters /etc/pmta/") ; 
                $sshConnector->cmd("mv  /home/keys /etc/pmta/"); 
                $sshConnector->cmd("mv  /home/bounces /etc/pmta/") ; 
                $sshConnector->cmd("mv  /home/delivered /etc/pmta/"); 
                $sshConnector->cmd("mv  /home/deffered /etc/pmta/"); 
                $sshConnector->cmd("mv  /home/vmtas /etc/pmta/") ;
                $sshConnector->cmd("mv  /home/config.rpmsave /etc/pmta/");
                $sshConnector->cmd("mv -f /home/config /etc/pmta/");
                $sshConnector->cmd("mkdir  /var/spool/pmta;");
                $sshConnector->cmd("mkdir  /var/spool/iresponse;");
                $sshConnector->cmd("mkdir  /var/spool/iresponse/pickup;");
                $sshConnector->cmd("mkdir  /var/spool/iresponse/bad;") ;
                if($version == 'v5' || $version == 'v45'){
                    $sshConnector->cmd("sed -i 's/#http-redirect-to-https false/http-redirect-to-https false/g' /etc/pmta/parameters/pmta_http.conf;");
                    $sshConnector->cmd("sed -i 's/http-mgmt-port ".$pmtaPort."/http-mgmt-port 8080/g' /etc/pmta/parameters/pmta_http.conf;"); 
                }
                else{
                    $sshConnector->cmd("sed -i 's/http-redirect-to-https false/#http-redirect-to-https false/g' /etc/pmta/parameters/pmta_http.conf;");
                    $sshConnector->cmd("sed -i 's/http-mgmt-port 8080/http-mgmt-port ".$pmtaPort."/g' /etc/pmta/parameters/pmta_http.conf;");
                }
                $sshConnector->cmd("mkdir  /var/spool/iresponse/tmp;") ;
                $sshConnector->cmd("chown -R pmta:pmta  /var/spool/pmta;");
                $sshConnector->cmd("chown -R pmta:pmta  /var/spool/iresponse;") ;
                $sshConnector->cmd("chown -R pmta:pmta  /var/spool/pmta/*;") ;
                $sshConnector->cmd("chown -R pmta:pmta  /var/spool/iresponse/*;") ;
                $sshConnector->cmd("chown -R pmta:pmta  /etc/pmta/*;") ;
                $sshConnector->cmd("chmod 777 /usr/sbin/pmta;");
                $sshConnector->cmd("chmod 777 /usr/sbin/pmtad;");
                $sshConnector->cmd("chmod 777 /usr/sbin/pmtahttpd;");
                $result .= $sshConnector->cmd('service pmta start;',true);
                $result .= $sshConnector->cmd('service pmta reload;',true);
                $result .= $sshConnector->cmd('service pmta restart;',true); 
                $result .= $sshConnector->cmd('service pmtahttp restart;',true); 
                if (strpos(strtolower($dataos['NAME']), 'centos') !== false || strpos(strtolower($dataos['NAME']), 'almalinux') !== false) {
                    $sshConnector->cmd('service httpd restart;'); 
                }else{
                    $sshConnector->cmd("a2ensite powermta"); 
                    $sshConnector->cmd("systemctl reload apache2"); 
                    $sshConnector->cmd("systemctl restart apache2");
                }
                


                $sshConnector->disconnect();
                
                //$result .= $sshConnector->cmd(';',true) . "\r\n";
                
            }

            Page::printApiResults(200,$result);
        }
        else
        {
            Page::printApiResults(500,'Incorrect server id !');
        }
    }
    
    /**
     * @name saveConfig
     * @description saveConfig action
     * @before init
     */
    public function saveConfig($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','configs');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serverId = intval($this->app->utils->arrays->get($parameters,'server-id'));

        if($serverId > 0)
        {
            $mtaServer = MtaServer::first(MtaServer::FETCH_ARRAY,['status = ? and id = ?',['Activated',$serverId]]);

            if(count($mtaServer) == 0)
            {
                Page::printApiResults(404,'Mta Server not found !');
            }

            $type = $this->app->utils->arrays->get($parameters,'type');

            if($type == null || $type == '')
            {
                Page::printApiResults(404,'Config type not found !');
            }

            $name = $this->app->utils->arrays->get($parameters,'name');

            if($name == null || $name == '')
            {
                Page::printApiResults(404,'Config not found !');
            }

            $content = $this->app->utils->arrays->get($parameters,'content');

            if($content == null || $content == '')
            {
                Page::printApiResults(404,'Config content found !');
            }

            # call iresponse api
            $result = Api::call('Pmta','saveConfig',['server-id' => $serverId,'type' => $type,'name' => $name,'content' => $content]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'Incorrect server id !');
        }
    }
    
    /**
     * @name executePmtaCommand
     * @description executePmtaCommand action
     * @before init
     */
    public function executePmtaCommand($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','commands');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serversBundle = [];
            
        $servers = $this->app->utils->arrays->get($parameters,'servers');
        $vmtas = $this->app->utils->arrays->get($parameters,'vmtas',[]);
        $queues = array_filter(explode('|',$this->app->utils->arrays->get($parameters,'queues','')));
        $target = $this->app->utils->arrays->get($parameters,'target','all');
        $command = $this->app->utils->arrays->get($parameters,'action');
        $scheduleTimes = intval($this->app->utils->arrays->get($parameters,'schedule-times'));
        $schedulePeriod = intval($this->app->utils->arrays->get($parameters,'schedule-period'));
        
        if(count($servers) > 0)
        {
            if($command == null || $command == '')
            {
                Page::printApiResults(404,'PowerMta command not found !');
            }

            if(count($queues) && $queues[0] == '*/*')
            {
                $queues = [];
            }
            
            foreach ($servers as $serverId) 
            {
                $tmp = [
                    'server' => $serverId,
                    'queues' => $queues,
                    'target' => $target,
                    'schedule-times' => $scheduleTimes,
                    'schedule-period' => $schedulePeriod,
                    'command' => $command
                ];

                $vms = [];

                if(is_array($vmtas) && count($vmtas))
                {
                    foreach ($vmtas as $vmta) 
                    {
                        $parts = explode("|",$vmta);

                        if(count($parts) && intval($serverId) == intval($parts[0]))
                        {
                            $vms[] = $vmta;
                        }
                    }
                }

                $tmp['vmtas'] = $vms;
                $serversBundle[] = $tmp;
            }
            
            # call iresponse api
            $result = Api::call('Pmta','executeCommand',['bundle' => $serversBundle]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,'',['logs' => $result['data']]);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }
 
    /**
     * @name startProcesses
     * @description startProcesses action
     * @before init
     */
    public function startProcesses($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','processes');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $serversIds = $this->app->utils->arrays->get($parameters,'servers');
        $vmtas = $this->app->utils->arrays->get($parameters,'vmtas',[]);
        $queues = array_filter(explode('|',$this->app->utils->arrays->get($parameters,'queues','')));
        $pauseTime = intval($this->app->utils->arrays->get($parameters,'pause-time'));
        $resumeTime = intval($this->app->utils->arrays->get($parameters,'resume-time'));
        
        if(is_array($serversIds) && count($serversIds) > 0)
        {
            $mtaServers = MtaServer::all(MtaServer::FETCH_ARRAY,['id IN ?',[$serversIds]],['id','provider_id','provider_name','name']);
            $processesIds = [];
            
            if(count($queues) && $queues[0] == '*/*')
            {
                $queues = [];
            }
            
            foreach ($mtaServers as $server) 
            {
                $vmtasIds = [];

                if(is_array($vmtas) && count($vmtas))
                {
                    foreach ($vmtas as $vmta) 
                    {
                        $parts = explode("|",$vmta);

                        if(count($parts) > 1 && intval($this->app->utils->arrays->get($server,'id')) == intval($parts[0]))
                        {
                            $vmtasIds[] = intval($parts[1]);
                        }
                    }
                }
                
                $process = new PmtaProcess([
                    'provider_id' => intval($this->app->utils->arrays->get($server,'provider_id')),
                    'provider_name' => $this->app->utils->arrays->get($server,'provider_name'),
                    'server_id' => intval($this->app->utils->arrays->get($server,'id')),
                    'server_name' => $this->app->utils->arrays->get($server,'name'),
                    'user_full_name' => Authentication::getAuthenticatedUser()->getFirstName() . ' ' . Authentication::getAuthenticatedUser()->getLastName(),
                    'queues' => implode(',',$queues),
                    'vmtas' => implode(',',$vmtasIds),
                    'pause_wait' => $pauseTime,
                    'resume_wait' => $resumeTime,
                    'action_start_time' => date('Y-m-d H:i:s')
                ]);
                
                $processesIds[] = $process->insert();
            }
            
            # call iresponse api
            $result = Api::call('Pmta','executeAutoPauseResumeProcess',['processes-ids' => $processesIds]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }

            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }

            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }
    
    /**
     * @name stopProcesses
     * @description stop pmta processes action
     * @before init
     */
    public function stopProcesses($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }

        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','processes');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $processesIds = $this->app->utils->arrays->get($parameters,'processes-ids',[]);

        if(!is_array($processesIds) || count($processesIds) == 0)
        {
            Page::printApiResults(500,'No processes found !');
        }
        
        # call iresponse api
        $result = Api::call('Pmta','stopAutoPauseResumeProcess',['processes-ids' => $processesIds]);

        if(count($result) == 0)
        {
            Page::printApiResults(500,'No response found !');
        }

        if($result['httpStatus'] == 500)
        {
            Page::printApiResults(500,$result['message']);
        }
            
        Page::printApiResults(200,$result['message']);
    }
    
    /**
     * @name updateGlobalVmtas
     * @description updateGlobalVmtas action
     * @before init
     */
    public function updateGlobalVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','globalVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $servers = $this->app->utils->arrays->get($parameters,'servers');
        $ispId = intval($this->app->utils->arrays->get($parameters,'isp-id'));
        $domain = $this->app->utils->arrays->get($parameters,'domain');
        
        if(count($servers) > 0)
        {
            if($ispId == null || $ispId == 0)
            {
                Page::printApiResults(500,'Isp not selected !');
            }
            
            if($domain == null || $domain == '')
            {
                Page::printApiResults(500,'Domain not inserted !');
            }

            # call iresponse api
            $result = Api::call('Pmta','updateGlobalVmtas',['action' => 'create','servers-ids' => $servers,'isp-id' => $ispId,'domain' => $domain]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }
    
    /**
     * @name resetGlobalVmtas
     * @description resetGlobalVmtas action
     * @before init
     */
    public function resetGlobalVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','globalVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $servers = $this->app->utils->arrays->get($parameters,'servers');
        $ispId = intval($this->app->utils->arrays->get($parameters,'isp-id'));
        
        if(count($servers) > 0)
        {
            # call iresponse api
            $result = Api::call('Pmta','updateGlobalVmtas',['action' => 'reset','servers-ids' => $servers,'isp-id' => $ispId]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }
    
     /**
     * @name updateIndividualVmtas
     * @description updateIndividualVmtas action
     * @before init
     */
    public function updateIndividualVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','individualVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $ipsDomains = array_filter(explode(PHP_EOL,strval($this->app->utils->arrays->get($parameters,'mapping',''))));
        $ispId = intval($this->app->utils->arrays->get($parameters,'isp-id'));
        
        if(count($ipsDomains) > 0)
        {
            if($ispId == null || $ispId == 0)
            {
                Page::printApiResults(500,'Isp not selected !');
            }
            
            $pairs = [];
            $mapping = [];
            $ipsFromMap = [];
            
            foreach ($ipsDomains as $row) 
            {
                if($this->app->utils->strings->contains($row,'|'))
                {
                    $ip = trim(strval($this->app->utils->arrays->first(explode('|',$row))));
                    $domain = trim(strval($this->app->utils->arrays->last(explode('|',$row))));
                    
                    if(filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) || filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6))
                    {
                        $ipsFromMap[] = $ip;
                        $pairs[$ip] = $domain;
                    }
                }
            }
            
            if(count($ipsFromMap) == 0)
            {
                Page::printApiResults(500,'no ips inserted !');
            }
            
            $vmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['ip in ?',[$ipsFromMap]],['mta_server_id','ip']);
            
            if(count($vmtas) == 0)
            {
                Page::printApiResults(500,'no ips found !');
            }
            
            foreach ($vmtas as $vmta) 
            {
                if(!key_exists($vmta['mta_server_id'],$mapping))
                {
                    $mapping[$vmta['mta_server_id']] = [];
                }
                
                $mapping[$vmta['mta_server_id']][] = [
                    'vmta-ip' => $vmta['ip'],
                    'domain' => $pairs[$vmta['ip']]
                ];
            }
            
            # call iresponse api
            $result = Api::call('Pmta','updateIndividualVmtas',['action' => 'create','mapping' => $mapping,'isp-id' => $ispId]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No ips / domains found !');
        }
    }
    
    /**
     * @name resetIndividualVmtas
     * @description resetIndividualVmtas action
     * @before init
     */
    public function resetIndividualVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','individualVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $ips = array_filter(explode(PHP_EOL,strval($this->app->utils->arrays->get($parameters,'ips',''))));
        $ispId = intval($this->app->utils->arrays->get($parameters,'isp-id'));

        if(count($ips) > 0)
        {
            if($ispId == null || $ispId == 0)
            {
                Page::printApiResults(500,'Isp not selected !');
            }

            $mapping = [];
            $ipsFromMap = [];
            
            foreach ($ips as $row) 
            {
                if($this->app->utils->strings->contains($row,'|'))
                {
                    $ip = trim(strval($this->app->utils->arrays->first(explode('|',$row))));

                    if(filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) || filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6))
                    {
                        $ipsFromMap[] = $ip;
                    }
                }
                else
                {
                    if(filter_var($this->app->utils->strings->trim($row),FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) || filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6))
                    {
                        $ipsFromMap[] = $this->app->utils->strings->trim($row);
                    }
                }
            }
            
            if(count($ipsFromMap) == 0)
            {
                Page::printApiResults(500,'no ips inserted !');
            }
            
            $vmtas = ServerVmta::all(ServerVmta::FETCH_ARRAY,['ip in ?',[$ipsFromMap]],['mta_server_id','ip']);
            
            if(count($vmtas) == 0)
            {
                Page::printApiResults(500,'no ips found !');
            }
            
            foreach ($vmtas as $vmta) 
            {
                if(!key_exists($vmta['mta_server_id'],$mapping))
                {
                    $mapping[$vmta['mta_server_id']] = [];
                }
                
                $mapping[$vmta['mta_server_id']][] = [
                    'vmta-ip' => $vmta['ip']
                ];
            }

            # call iresponse api
            $result = Api::call('Pmta','updateIndividualVmtas',['action' => 'reset','mapping' => $mapping,'isp-id' => $ispId]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No ips found !');
        }
    }
    
    /**
     * @name createSMTPVmtas
     * @description createSMTPVmtas action
     * @before init
     */
    public function createSMTPVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','smtpVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $servers = $this->app->utils->arrays->get($parameters,'servers');
        $smtps = array_filter(explode(PHP_EOL,$this->app->utils->arrays->get($parameters,'smtps','')));
        
        if(count($servers) > 0)
        {
            if(count($smtps) == 0)
            {
                Page::printApiResults(500,'No mailboxes found !');
            }
            
            $smtpsList = [];
            
            foreach ($smtps as $smtp)
            {
                $smtpParts = array_filter(explode(' ',$smtp));
                
                if(count($smtpParts) == 4)
                {
                    $smtpsList[] = [
                        'host' => $smtpParts[0],
                        'port' => $smtpParts[1],
                        'username' => $smtpParts[2],
                        'password' => $smtpParts[3]
                    ];
                }
            }
            
            if(count($smtpsList) == 0)
            {
                Page::printApiResults(500,'No smtp found !');
            }
            
            # call iresponse api
            $result = Api::callpmta('Pmta','createSMTPVmtas',['servers-ids' => $servers,'smtps-list' => $smtpsList]);
            
            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }
    
    /**
     * @name resetSMTPVmtas
     * @description resetSMTPVmtas action
     * @before init
     */
    public function resetSMTPVmtas($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !');
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Pmta','smtpVmtas');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $servers = $this->app->utils->arrays->get($parameters,'servers');
        
        if(count($servers) > 0)
        { 
            # call iresponse api
            $result = Api::call('Pmta','resetSMTPVmtas',['servers-ids' => $servers]);

            if(count($result) == 0)
            {
                Page::printApiResults(500,'No response found !');
            }
            
            if($result['httpStatus'] == 500)
            {
                Page::printApiResults(500,$result['message']);
            }
            
            Page::printApiResults(200,$result['message']);
        }
        else
        {
            Page::printApiResults(500,'No servers found !');
        }
    }


    /**
     * @name accountings
     * @description accountings action
     * @before init
     */
    public function parseTexOSt($text) {
             $data = array();
  
            // Split the text by lines
             $lines = explode("\n", trim($text));
  
             // Loop through each line
             foreach ($lines as $line) {
               // Extract key and value using explode
               $parts = explode("=", $line);
               // Remove quotes from key and value (trim)
               $key = trim(str_replace('"', '', $parts[0]));
               $value = trim(str_replace('"', '', $parts[1]));
    
               // Store data in an associative array
               $data[$key] = $value;
          }
  
             // Return the parsed data
             return $data;
           }
    
    /**
     * @name accountings
     * @description accountings action
     * @before init
     */
    public function accountings($parameters = []) 
    { 
        $stats = json_decode(base64_decode($this->app->utils->arrays->get($parameters,'stats')),true);
        $bounceEmails = json_decode(base64_decode($this->app->utils->arrays->get($parameters,'bounce-emails')),true);
        $cleanEmails = json_decode(base64_decode($this->app->utils->arrays->get($parameters,'clean-emails')),true);
        
        # calculate stats
        if(count($stats))
        { 
            foreach ($stats as $processId => $processStats) 
            {
                if(count($processStats))
                {
                    if(key_exists('total', $processStats) && key_exists('type', $processStats))
                    {
                        
                        # save totals 
                        $delivered = intval($processStats['total']['delivered']);
                        $softBounced = intval($processStats['total']['soft_bounced']);
                        $hardBounced = intval($processStats['total']['hard_bounced']);
                        
                        if($delivered > 0 || $softBounced > 0 || $hardBounced > 0)
                        {
                            $this->app->database('system')->execute("UPDATE production.mta_processes SET delivered = delivered + " . $delivered . " , soft_bounced = soft_bounced + " . $softBounced . " , hard_bounced = hard_bounced + " . $hardBounced . " WHERE id = {$processId}");
                        }
                        
                        if($processStats['type'] == 'md')
                        {
                            foreach ($processStats as $vmtaId => $vmtasStats) 
                            {
                                if($vmtaId != 'total' && $vmtaId != 'type' && count($vmtasStats))
                                {
                                    $delivered = intval($processStats[$vmtaId]['delivered']);
                                    $softBounced = intval($processStats[$vmtaId]['soft_bounced']);
                                    $hardBounced = intval($processStats[$vmtaId]['hard_bounced']);

                                    if($delivered > 0 || $softBounced > 0 || $hardBounced > 0)
                                    {
                                        $this->app->database('system')->execute("UPDATE production.mta_processes_ips SET delivered = delivered + " . $delivered . " , soft_bounced = soft_bounced + " . $softBounced . " , hard_bounced = hard_bounced + " . $hardBounced . " WHERE process_id = {$processId} AND server_vmta_id = {$vmtaId}");
                                    }
                                }
                            }
                        }
                    }
                }  
            }
        }

        # calculate bounce and clean emails
        if(count($bounceEmails) > 0 || count($cleanEmails) > 0)
        {   
            # prepare lists 
            $lists = [];
            
            # get lists from db
            $dataLists = $this->app->database('system')->query()->from('lists.data_lists')->all();
            
            if(count($dataLists) == 0)
            {
                Page::printApiResults(500,'No data lists found !');
            }
            
            foreach ($dataLists as $row) 
            {
                $lists[intval($row['id'])] = strtolower($row['table_schema'] . '.' . $row['table_name']);
            }

            # connect to clients database
            $this->app->database('clients')->connect();
            
            # update clean emails
            foreach ($cleanEmails as $row) 
            {
                if(count($row))
                {
                    $parsed = explode('_',$row);
                    $listId = intval($this->app->utils->arrays->get($parsed,0));
                    $clientId = intval($this->app->utils->arrays->get($parsed,1));
                    
                    if(key_exists($listId,$lists))
                    {
                        $parsed = explode('.',$lists[$listId]);
                        $schema = $this->app->utils->arrays->get($parsed,0);
                        $table = $this->app->utils->arrays->get($parsed,1);
                            
                        if(Table::exists('clients',$table,$schema))
                        {
                            # update client flags
                            $this->app->database('clients')->execute("UPDATE {$lists[$listId]} SET is_clean = 't' , is_fresh = 'f' WHERE id = $clientId AND is_fresh = 't'");
                        }
                    }
                }
            }
            
            # update bounce emails
            foreach ($bounceEmails as $row) 
            {
                if(count($row))
                {
                    $parsed = explode('_',$row);
                    $listId = intval($this->app->utils->arrays->get($parsed,0));
                    $clientId = intval($this->app->utils->arrays->get($parsed,1));
                    
                    if(key_exists($listId,$lists))
                    {
                        $parsed = explode('.',$lists[$listId]);
                        $schema = $this->app->utils->arrays->get($parsed,0);
                        $table = $this->app->utils->arrays->get($parsed,1);
                            
                        if(Table::exists('clients',$table,$schema))
                        {
                            # update client flags
                            $this->app->database('clients')->execute("UPDATE {$lists[$listId]} SET is_hard_bounced = 't' WHERE id = $clientId");
                        }
                    }
                }
            }
        }
        
        Page::printApiResults(200,'Operation completed successfully !');
    }
}