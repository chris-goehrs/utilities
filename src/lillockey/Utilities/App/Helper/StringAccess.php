<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 2/4/2016
 * Time: 3:24 PM
 */

namespace lillockey\Utilities\App\Helper;


use lillockey\Utilities\App\InstanceHolder;

class StringAccess
{
	public function get($key)
	{
		return InstanceHolder::string($key);
	}
}