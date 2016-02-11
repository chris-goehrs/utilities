<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/3/2016
 * Time: 11:27 AM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


interface DelimitedFetchable
{
    /**
     * Used to retrieve the text associated with this document
     * @return string
     */
    public function fetch();
}