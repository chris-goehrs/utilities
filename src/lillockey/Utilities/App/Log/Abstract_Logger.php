<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 11:11 AM
 */

namespace lillockey\Utilities\App\Log;

use lillockey\Utilities\App\Abstract_Utilities_Named_App_Class;
use lillockey\Utilities\App\AbstractUtility;
use lillockey\Utilities\Config\AbstractCustomConfig;

abstract class Abstract_Logger extends AbstractUtility implements Loggable
{
	protected $config = null;

	public function __construct(AbstractCustomConfig &$config, $name)
	{
		$this->config = $config;
		parent::__construct($name);
	}

	/**
	 * Writes $_REQUEST nvp set to log with each key/value set being written as a new line
	 */
	public function write_request_fields_to_log()
	{
		$this->write_array_to_log($_REQUEST);
	}

	/**
	 * @param array $ar
	 */
	public function write_array_to_log(array $ar)
	{
		//Get the maximum field length
		$max_length = 0;
		foreach($ar as $key=>$value) if(strlen($key) > $max_length) $max_length = strlen($key);

		//Add the fields to the log
		foreach($ar as $key=>$value){
			if(is_array($value))
				$valuep = "array(".sizeof($value).")";
			else
				$valuep = $value;

			$key_pad = str_pad($key, $max_length);
			$this->write_to_log("    $key_pad => $valuep");
		}
	}
}