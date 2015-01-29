<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 8:09 PM
 */

namespace lillockey\Utilities\Config;

/**
 * Class FullCustomConfig
 * @package Missilesilo\Utilities\Config
 */
class FullCustomConfig extends DefaultCustomConfig
{
    public function __construct($host, $user, $pass, $db)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db   = $db;
    }
} 