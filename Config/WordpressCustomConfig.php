<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 8:00 PM
 */

namespace Missilesilo\Utilities\Config;

/**
 * Class WordpressCustomConfig
 * @package Missilesilo\Utilities\Config
 */
final class WordpressCustomConfig extends AbstractCustomConfig
{

    public function __construct($wp_db_prefix = 'wp_')
    {
        if(defined('DB_HOST')) 		$this->host = DB_HOST;
        if(defined('DB_NAME')) 		$this->name = DB_NAME;
        if(defined('DB_USER')) 		$this->user = DB_USER;
        if(defined('DB_PASSWORD')) 	$this->pass = DB_PASSWORD;
                                    $this->wp_db_prefix = $wp_db_prefix;
    }
} 