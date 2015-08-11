<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 8:00 PM
 */

namespace lillockey\Utilities\Config;

/**
 * Class WordpressCustomConfig
 *
 * @package lillockey\Utilities\Config
 */
final class WordpressCustomConfig extends AbstractCustomConfig
{
    private $wp_db_prefix = '';

    public function __construct($wp_db_prefix = '')
    {
        if(defined('DB_HOST')) 		$this->host = DB_HOST;
        if(defined('DB_NAME')) 		$this->db =   DB_NAME;
        if(defined('DB_USER')) 		$this->user = DB_USER;
        if(defined('DB_PASSWORD')) 	$this->pass = DB_PASSWORD;

        $this->wp_db_prefix = $wp_db_prefix;

        parent::__construct();
    }

    /**
     * @param $table
     * @return mixed|string
     */
    public function table($table)
    {
        return "{$this->wp_db_prefix}$table";
    }
}