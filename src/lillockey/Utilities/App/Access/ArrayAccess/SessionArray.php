<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 2/7/2016
 * Time: 1:51 AM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\Exceptions\NotAnArrayException;

class SessionArray extends AccessibleArray
{
	/**
	 * @throws NotAnArrayException
	 */
	public function __construct()
	{
		if(!isset($_SESSION) || !is_array($_SESSION)){
			session_start();
		}

		//Leave these in here just in case
		if(!isset($_SESSION)) throw new NotAnArrayException('$_SERVER is not a valid array');
		if(!is_array($_SESSION)) throw new NotAnArrayException('$_SERVER is not a valid array');
		parent::__construct($_SESSION);
	}
}