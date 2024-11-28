<?php declare(strict_types=1); namespace IR\App\Models\Admin; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            ServerVmta.php	
 */

# orm 
use IR\Orm\ActiveRecord as ActiveRecord;

# helpers
use IR\App\Helpers\Authentication as Authentication;
use IR\App\Helpers\Permissions as Permissions;

/**
 * @name ServerVmta
 * @description ServerVmta Model
 */
class ServerVmta extends ActiveRecord
{
    /**
     * @database
     * @readwrite
     */
    protected $_databaseKey = 'system';

    /**
     * @schema
     * @readwrite
     */
    protected $_schema = 'admin';

    /**
     * @table
     * @readwrite
     */
    protected $_table = 'servers_vmtas';

    # columns

    /**
     * @column
     * @readwrite
     * @primary
     * @indexed
     * @autoincrement
     * @type integer
     * @nullable false
     * @length 
     */
    protected $_id;

    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable false
     * @length
     */
    protected $_mta_server_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 200
     */
    protected $_mta_server_name;
    
    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable true
     * @length
     */
    protected $_isp_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable true
     * @length 200
     */
    protected $_isp_name;
    
    /**
     * @column
     * @readwrite
     * @indexed
     * @type text
     * @nullable false
     * @length 20
     */
    protected $_status;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 100
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 100
     */
    protected $_name;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 100
     */
    protected $_domain;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable true
     * @length 100
     */
    protected $_custom_domain;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 100
     */
    protected $_ip;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable true
     * @length 100
     */
    protected $_ping_status;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 200
     */
    protected $_created_by;

    /**
     * @column
     * @readwrite
     * @type text
     * @nullable true
     * @length 200
     */
    protected $_last_updated_by;

    /**
     * @column
     * @readwrite
     * @type date
     * @nullable false
     * @length 
     */
    protected $_created_date;

    /**
     * @column
     * @readwrite
     * @type date
     * @nullable true
     * @length 
     */
    protected $_last_updated_date;

    # overrided
    
    /**
     * @name first
     * @description returns the first matched record
     * @access static
     * @param integer $format
     * @param array $where
     * @param array $fields
     * @param string $order
     * @param string $direction
     * @return mixed
     */
    public static function first(int $format = ActiveRecord::FETCH_ARRAY, array $where = [], array $fields = ["*"],string $order = '', string $direction = 'ASC')
    {
        $user = Authentication::getAuthenticatedUser();
        
        if($user->getMasterAccess() != 'Enabled')
        {
            Permissions::modelTeamAuthsFilter(__CLASS__,$user,$where);
        }
        
        return parent::first($format,$where, $fields, $order, $direction);
    }

    /**
     * @name all
     * @description creates a query, taking into account the various filters and ﬂags, to return all matching records.
     * @access static
     * @param integer $format
     * @param array $where
     * @param array $fields
     * @param string $order
     * @param string $direction
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public static function all(int $format = ActiveRecord::FETCH_ARRAY, array $where = [], array $fields = ["*"], string $order = '', string $direction = 'ASC', int $limit = 0, int $offset = 0) 
    {
        $user = Authentication::getAuthenticatedUser();
        
        if($user->getMasterAccess() != 'Enabled')
        {
            Permissions::modelTeamAuthsFilter(__CLASS__,$user,$where);
        }

        return parent::all($format, $where, $fields, $order, $direction, $limit, $offset);
    }
    
    /**
     * @name load
     * @description loads a record if the primary column’s value has been provided
     * @access public
     * @return
     */
    public function load($primayValue = null) 
    {
        $user = Authentication::getAuthenticatedUser();
        
        if($user->getMasterAccess() != 'Enabled')
        {
            $where = [];
            $teamBasedFilterIds = Permissions::modelTeamAuthsFilter(__CLASS__,$user,$where);
            $hasAdminRole = Permissions::hasAdminBasedRole($user);
            
            if($hasAdminRole == false)
            {
                if($primayValue == null)
                {
                    if(in_array($this->_id,$teamBasedFilterIds))
                    {
                        parent::load();
                    }
                }
                else
                {
                    if(in_array($primayValue,$teamBasedFilterIds))
                    {
                        parent::load($primayValue);
                    }
                }
            }
            else
            {
                parent::load($primayValue);
            }
        }
        else
        {
            parent::load($primayValue);
        }
    }
}


