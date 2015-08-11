<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 1:16 PM
 */

namespace lillockey\Utilities\App;


class Abstract_Utilities_Named_App_Class
{
	protected $name;

	public function __construct($name)
	{
		$this->name = $name;
	}
}