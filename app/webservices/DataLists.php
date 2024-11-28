<?php declare(strict_types=1); namespace IR\App\Webservices; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            DataLists.php	
 */

# core 
use IR\Core\Base as Base;
use IR\Core\Application as Application;

# models
use IR\App\Models\Lists\DataList as DataList; 

# orm 
use IR\Orm\Table as Table;

# helpers 
use IR\App\Helpers\Authentication as Authentication;
use IR\App\Helpers\Permissions as Permissions;
use IR\App\Helpers\Page as Page;

/**
 * @name DataLists
 * @description DataLists WebService
 */
class DataLists extends Base
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
     * @name getEmailsLists
     * @description get emails lists action
     * @before init
     */
    public function getEmailsLists($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !'); 
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'DataLists','main')
                  || Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'Offers','suppression');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $dataProvidersIds = $this->app->utils->arrays->get($parameters,'data-provider-ids',[]);
        $ispsIds = $this->app->utils->arrays->get($parameters,'isp-ids',[]);
        $conditions = [];
        $values = [];

        if(is_array($dataProvidersIds) && count($dataProvidersIds))
        {
            $conditions[] = 'data_provider_id IN ?';
            $values[] = $dataProvidersIds;
        }
        else if(intval($dataProvidersIds) > 0)
        {
            $conditions[] = 'data_provider_id = ?';
            $values[] = intval($dataProvidersIds);
        }

        if(is_array($ispsIds) && count($ispsIds))
        {
            $conditions[] = 'isp_id IN ?';
            $values[] = $ispsIds;
        }
        else
        {
            $conditions[] = 'isp_id = ?';
            $values[] = intval($ispsIds); 
        }

        # fetch lists
        $dataLists = count($values) > 0 ? DataList::all(DataList::FETCH_ARRAY,[implode(' AND ',$conditions),$values]) : DataList::all(DataList::FETCH_ARRAY);
        Page::printApiResults(200,'',['data-lists' => $dataLists]);
    }
    
    /**
     * @name fetchEmails
     * @description get emails action
     * @before init
     */
    public function fetchEmails($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !'); 
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'DataLists','emailsFetch');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $ids = array_filter(array_unique(explode(PHP_EOL,$this->app->utils->arrays->get($parameters,'ids',''))));
        
        if(!is_array($ids) || count($ids) == 0)
        {
            Page::printApiResults(500,'No ids inserted !');
        }
        
        $emails = [];
        
        # connect to the database 
        $this->app->database('clients')->connect();
        
        foreach ($ids as $id)
        {
            $parts = explode('_', trim($id));
            
            if(count($parts))
            {
                $list = DataList::first(DataList::FETCH_ARRAY,['id = ?',intval($parts[0])]);
                
                if(count($list))
                {
                    $res = $this->app->database('clients')->execute("SELECT email FROM {$list['table_schema']}.{$list['table_name']} WHERE id = {$parts[1]}");

                    if(count($res))
                    {
                        $emails[] = $res[0]['email'];
                    }
                }
            }
        }
        
        if(count($emails) == 0)
        {
            Page::printApiResults(500,'No emails found !');
        }
        
        Page::printApiResults(200,'Emails fetched successfully !',['emails' => implode(PHP_EOL,$emails)]);
    }
    
    /**
     * @name blacklistEmails
     * @description black emails action
     * @before init
     */
    public function blacklistEmails($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !'); 
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'DataLists','emailsFetch');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $emails = array_filter(array_unique(explode(PHP_EOL,$this->app->utils->arrays->get($parameters,'emails',''))));
        
        if(!is_array($emails) || count($emails) == 0)
        {
            Page::printApiResults(500,'No emails inserted !');
        }

        # connect to the database 
        $this->app->database('clients')->connect();
        
        # turn it into md5
        foreach ($emails as $key => $value)
        {
            $emails[$key] = $this->app->utils->strings->contains($value,'@') ? trim(md5($value)) : trim($value);
        }
        
        $tables = Table::available('clients');

        if(count($tables))
        {
            foreach ($tables as $table)
            {
                if($this->app->utils->strings->contains($table,'specials') == false && $this->app->utils->strings->contains($table,'suppressions')  == false)
                {
                    $this->app->database('clients')->execute("UPDATE {$table} SET is_blacklisted = 't' WHERE email_md5 IN ('" . implode("','",$emails) . "')");
                }
            }
        }
 
        Page::printApiResults(200,'Emails blacklisted successfully !');
    }
    
    /**
     * @name deleteEmails
     * @description delete emails action
     * @before init
     */
    public function deleteEmails($parameters = []) 
    { 
        # check for authentication
        if(!Authentication::isUserAuthenticated())
        {
            Page::printApiResults(401,'Only logged-in access allowed !'); 
        }
        
        # check users roles 
        Authentication::checkUserRoles();
        
        # check for permissions
        $access = Permissions::checkForAuthorization(Authentication::getAuthenticatedUser(),'DataLists','emailsFetch');

        if($access == false)
        {
            Page::printApiResults(403,'Access Denied !');
        }
        
        $emails = array_filter(array_unique(explode(PHP_EOL,$this->app->utils->arrays->get($parameters,'emails',''))));
        
        if(!is_array($emails) || count($emails) == 0)
        {
            Page::printApiResults(500,'No emails inserted !');
        }

        # connect to the database 
        $this->app->database('clients')->connect();
        
        # turn it into md5
        foreach ($emails as $key => $value)
        {
            $emails[$key] = $this->app->utils->strings->contains($value,'@') ? trim(md5($value)) : trim($value);
        }
        
        $tables = Table::available('clients');
        
        if(count($tables))
        {
            foreach ($tables as $table)
            {
                if($this->app->utils->strings->contains($table,'specials') == false && $this->app->utils->strings->contains($table,'suppressions')  == false)
                {
                    $this->app->database('clients')->execute("DELETE FROM {$table} WHERE email_md5 IN ('" . implode("','",$emails) . "')");
                }
            }
        }

        Page::printApiResults(200,'Emails removed successfully !');
    }
}