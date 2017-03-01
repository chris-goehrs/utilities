<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 5/24/2016
 * Time: 11:54 AM
 */

namespace lillockey\Utilities\App\Wordpress;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;
use lillockey\Utilities\Exceptions\NotAnArrayException;

class WordpressOptionsFullArray extends AccessibleArray
{
    public function __construct()
    {
        if(!function_exists('wp_load_alloptions')) throw new NotAnArrayException("This doesn't appeart to be wordpress");
        $options = wp_load_alloptions();
        if(!isset($options)) throw new NotAnArrayException('$options is not a valid array');
        if(!is_array($options)) throw new NotAnArrayException('$options is not a valid array');
        parent::__construct($options);
    }
}