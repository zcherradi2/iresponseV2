<?php declare(strict_types=1); namespace IR\App\Models\Production; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            MtaProcessIp.php	
 */

# orm 
use IR\Orm\ActiveRecord as ActiveRecord;

/**
 * @name MtaProcessIp
 * @description MtaProcessIp Model
 */
class MtaProcessIp extends ActiveRecord
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
    protected $_schema = 'production';

    /**
     * @table
     * @readwrite
     */
    protected $_table = 'mta_processes_ips';

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
    protected $_process_id;
    
    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable false
     * @length
     */
    protected $_server_vmta_id;

     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_sent_total;
    
    /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_delivered;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_hard_bounced;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_soft_bounced;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_opens;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_clicks;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_leads;
     
     /**
     * @column
     * @readwrite
     * @type integer
     * @nullable true
     * @length 
     */
     protected $_unsubs;
}