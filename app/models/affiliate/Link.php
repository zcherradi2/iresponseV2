<?php declare(strict_types=1); namespace IR\App\Models\Affiliate; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            Link.php	
 */

# orm 
use IR\Orm\ActiveRecord as ActiveRecord;

/**
 * @name Link
 * @description Link Model
 */
class Link extends ActiveRecord
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
    protected $_schema = 'affiliate';

    /**
     * @table
     * @readwrite
     */
    protected $_table = 'links';

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
     * @type text
     * @nullable false
     * @length 20
     */
    protected $_status;
    
    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable false
     * @length
     */
    protected $_affiliate_network_id;

    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable false
     * @length
     */
    protected $_offer_id;
    
    /**
     * @column
     * @readwrite
     * @indexed
     * @type integer
     * @nullable false
     * @length
     */
    protected $_creative_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 10
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @nullable false
     * @length 500
     */
    protected $_value;

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
}


