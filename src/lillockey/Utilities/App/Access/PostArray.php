<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/6/2015
 * Time: 1:30 PM
 */

namespace lillockey\Utilities\App\Access;


use lillockey\Utilities\Exceptions\NotAnArrayException;

class PostArray extends ArrayAccess
{
    public function __construct()
    {
        if(!isset($_POST)) throw new NotAnArrayException('$_POST is not a valid array');
        if(!is_array($_POST)) throw new NotAnArrayException('$_POST is not a valid array');
        parent::__construct($_POST);
    }
}