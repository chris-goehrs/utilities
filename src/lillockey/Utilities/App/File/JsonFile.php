<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 12:57 PM
 */

namespace lillockey\Utilities\App\File;


use lillockey\Utilities\App\Access\ArrayAccess\JsonArray;

class JsonFile extends JsonArray
{
    public function __construct(File $path)
    {
        if($path != null && $path->exists() && $path->is_file() && $path->is_readable())
            parent::__construct($path->get_contents());
        else
            parent::__construct(null);
    }
}