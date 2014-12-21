<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 8:09 PM
 */

namespace Missilesilo\Utilities\Config;


class FullCustomConfig extends AbstractCustomConfig
{
    public function __construct($host, $user, $pass, $db)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db   = $db;
    }
} 