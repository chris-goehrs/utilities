<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/6/2015
 * Time: 10:44 AM
 */

namespace lillockey\Utilities\App\Access;


use lillockey\Utilities\Exceptions\NotAnArrayException;

class GetArray extends ArrayAccess
{
    public function __construct()
    {
        if(!isset($_GET)) throw new NotAnArrayException('$_GET is not a valid array');
        if(!is_array($_GET)) throw new NotAnArrayException('$_GET is not a valid array');
        parent::__construct($_GET);
    }
}