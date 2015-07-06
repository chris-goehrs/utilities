<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 6/14/2015
 * Time: 5:43 PM
 */

namespace lillockey\Utilities\App;


use lillockey\Utilities\Config\AbstractCustomConfig;

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
	private static $instances = array();

	/**
	 * Stores an instance of AbstractCustomConfig using the name provided
	 * @param string               $name
	 * @param AbstractCustomConfig $config
	 */
	public static function add_config($name, AbstractCustomConfig $config)
	{
		self::$configs[$name] = $config;
	}

	/**
	 * Gets the named configuration
	 * @param string $name - the name of the configuration
	 * @return AbstractCustomConfig|null
	 */
	public static function get_config($name)
	{
		if(array_key_exists($name, self::$configs))
			return self::$configs[$name];

		return null;
	}

	/**
	 * Gets the named instance of the Utilities class
	 * @param string $name - the name of the configuration to use
	 * @return Utilities|null
	 */
	public static function get_instance($name)
	{
		if(array_key_exists($name, self::$instances)){
			return self::$instances[$name];
		}

		if($config = self::get_config($name)){
			return self::$instances[$name] = new Utilities($config);
		}else{
			return null;
		}
	}
}