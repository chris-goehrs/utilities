<?php

namespace lillockey\Utilities\Config;

/**
 * Class AbstractCustomConfig
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

    //Query cacheing
    public $db_use_query_cache = false;
    public $db_cache_text = null;

    //Other Settings
    public $db_throw_exceptions = true;

    //Log-Related =======================================================
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