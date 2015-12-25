<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/24/2015
 * Time: 5:26 PM
 */

namespace lillockey\Utilities\App\Helper;


interface StringComparable
{
	/**
	 * Retrieves the string used in compare_string
	 * @return string
	 */
	public function compare_get_string();
}