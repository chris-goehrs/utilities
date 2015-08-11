<?php

namespace lillockey\Utilities\Config;

/**
 * Class AbstractCustomConfig
 *
 * @property string host - The database provider's host name (ip address or domain)
 * @property string user - The username for the database
 * @property string pass - The password for the selected user for the database
 * @property string db - The name of the database being accessed
 * @property string db_type - The connection type for the DSN for PDO
 *
 *
 * @package Missilesilo\Utilities\Config
 */
abstract class AbstractCustomConfig
{
    //Database-Related ==================================================
    //Credentials
	public $host = 'localhost';
	public $user = 'root';
	public $pass = null;
	public $db = null;
	public $db_type = 'mysql';

    //Query cacheing
    public $db_use_query_cache = false;
    public $db_cache_text = null;

    //Other Settings
    public $db_throw_exceptions = true;

    //Log-Related =======================================================
    public $log_type = 'file';
    public $log_location = './general_utilities.log';
    public $allow_write_to_log = true; //TODO: Turn this off when live
    public $db_log_queries = true;

    /**
     * @return bool - true if credentials are valid/false if not
     */
    public function db_credentials_are_valid()
    {
        return $this->host !== null && $this->db !== null && $this->user !== null;
    }

    public function __construct()
    {
        if($this->db_use_query_cache) $this->db_cache_text = 'SQL_CACHE ';
    }

    /**
     * Processes the table name in some form or another (like wordpress adding wp_) to the beginning
     * @param $table
     * @return mixed
     */
    public abstract function table($table);

}