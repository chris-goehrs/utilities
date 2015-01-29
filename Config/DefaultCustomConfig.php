<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 7:59 PM
 *
 * Use this class when no database interactivity is needed or you intend to set each variable manually.
 */

namespace lillockey\Utilities\Config;

/**
 * Class DefaultCustomConfig
 * @package Missilesilo\Utilities\Config
 */
class DefaultCustomConfig extends AbstractCustomConfig
{
    public function table($table)
    {
        return $table;
    }
} 