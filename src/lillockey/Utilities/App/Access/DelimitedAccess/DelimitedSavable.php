<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/3/2016
 * Time: 11:22 AM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


interface DelimitedSavable
{
    /**
     * Saves to the stored or given path
     * @param string $save_path
     * @return mixed
     */
    public function save($save_path = null);
}