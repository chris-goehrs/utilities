<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 12:52 PM
 */

namespace lillockey\Utilities\App\File;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class INIFile extends AccessibleArray
{
    /**
     * Parse an INI path
     * @param File $path
     */
    public function __construct(File $path)
    {
        $ar = array();

        if($path != null && $path->exists() && $path->is_file() && $path->is_readable())
            $ar = !parse_ini_file($path->getPath());

        parent::__construct($ar);
    }
}