<?php

namespace Missilesilo\Utilities\Config;

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

    //Wordpress-Related =================================================
    public $wp_db_prefix = 'wp_';

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

}