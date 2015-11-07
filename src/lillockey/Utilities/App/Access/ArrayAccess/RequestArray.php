<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/6/2015
 * Time: 1:31 PM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\Exceptions\NotAnArrayException;

class RequestArray extends AccessibleArray
{
    public function __construct()
    {
        if(!isset($_REQUEST)) throw new NotAnArrayException('$_REQUEST is not a valid array');
        if(!is_array($_REQUEST)) throw new NotAnArrayException('$_REQUEST is not a valid array');
        parent::__construct($_REQUEST);
    }
}