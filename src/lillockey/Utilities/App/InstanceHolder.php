<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 6/14/2015
 * Time: 5:43 PM
 */

namespace lillockey\Utilities\App;

use lillockey\Utilities\App\Log\File_Logger;
use lillockey\Utilities\Config\AbstractCustomConfig;

define ('INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME', sha1(time()));

/**
 * Class InstanceHolder
 *
 * @staticvar array $configs AbstractCustomConfig[]
 * @staticvar array $instances Utilities[]
 *
 * @package lillockey\Utilities\App
 */
class InstanceHolder
{
	private static $configs = array();
	private static $db_instances = array();
	private static $util_instance = null;
	private static $log_instances = array();
	private static $locality_insances = array();
	private static $image_instances = array();

	/**
	 * Stores an instance of AbstractCustomConfig using the name provided
	 * @param AbstractCustomConfig $config
	 * @param string               $name
	 */
	public static function set_config(AbstractCustomConfig $config, $name = null)
	{
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;
		self::$configs[$name] = $config;
	}

	/**
	 * Gets the named configuration
	 * @param string $name - the name of the configuration
	 * @return AbstractCustomConfig|null
	 */
	public static function &config($name = null)
	{
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;

		if(array_key_exists($name, self::$configs))
			return self::$configs[$name];

		return null;
	}

	/**
	 * Retrieves an instance of the utilities class
	 * @return Utilities
	 */
	public static function util()
	{
		if(self::$util_instance == null)
			self::$util_instance = new Utilities();

		return self::$util_instance;
	}

	/**
	 * @param null $name
	 * @return DB|null
	 */
	public static function db($name = null)
	{
		if(!self::pdo_enabled()) return null;
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;

		if(array_key_exists($name, self::$db_instances)){
			return self::$db_instances[$name];
		}

		if($config = self::config($name)){
			return self::$db_instances[$name] = new DB($config, $name);
		}else{
			return null;
		}
	}

	/**
	 * Gets the named instance of the Utilities class
	 * @param string $name - the name of the configuration to use
	 * @return Locality|null
	 */
	public static function locality($name = null)
	{
		if(!self::pdo_enabled()) return null;
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;

		if(array_key_exists($name, self::$locality_insances)){
			return self::$locality_insances[$name];
		}

		if($db = self::db($name)){
			//It has the db.  Let's make a new instance
			if($locality = self::$locality_insances[$name] = new Locality($db, $name)) return $locality;
		}

		return null;
	}

	public static function log($name = null)
	{
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;

		if(array_key_exists($name, self::$log_instances)){
			return self::$log_instances[$name];
		}

		if($config = self::config($name)){
			switch($config->log_type){
				case 'file':
					return self::$log_instances[$name] = new File_Logger($config, $name);
				default:
					return null;
			}
		}else{
			return null;
		}
	}

	public static function image($name = null)
	{
		if($name == null) $name = INSTANCE_HOLDER__DEFAULT_CONFIGURATION_NAME;

		if(array_key_exists($name, self::$image_instances)){
			return self::$image_instances[$name];
		}

		return self::$image_instances[$name] = new Image($name);
	}

	public static function pdo_enabled()
	{
		return extension_loaded('PDO');
	}

	public static function gd_enabled()
	{
		return extension_loaded('gd');
	}

	public static function fileinfo_enabled()
	{
		return extension_loaded('fileinfo');
	}
}