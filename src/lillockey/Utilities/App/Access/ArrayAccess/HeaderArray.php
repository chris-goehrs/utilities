<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 5/24/2016
 * Time: 10:33 AM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\Exceptions\NotAnArrayException;

class HeaderArray extends AccessibleArray
{
    public function __construct()
    {
        $headers = getallheaders();
        if(!isset($headers)) throw new NotAnArrayException('getallheaders() is not a valid array');
        if(!is_array($headers)) throw new NotAnArrayException('getallheaders() is not a valid array');
        parent::__construct($headers);
    }
}