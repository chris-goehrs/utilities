<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 6/14/2015
 * Time: 5:43 PM
 */

namespace lillockey\Utilities\App;

use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;
use lillockey\Utilities\App\Access\ArrayAccess\GetArray;
use lillockey\Utilities\App\Access\ArrayAccess\HeaderArray;
use lillockey\Utilities\App\Access\ArrayAccess\PostArray;
use lillockey\Utilities\App\Access\ArrayAccess\RequestArray;
use lillockey\Utilities\App\Access\ArrayAccess\ServerArray;
use lillockey\Utilities\App\Access\ArrayAccess\SessionArray;
use lillockey\Utilities\App\Access\ObjectAccessible;
use lillockey\Utilities\App\Helper\StringAccess;
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

        $nullval = null;
		return $nullval;
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

    private static $get = null;
    private static $post = null;
    private static $request = null;
    private static $server = null;
	private static $session = null;
    private static $headers = null;

    /**
     * Gets/instantiates an instance of the $_GET array as an AccessibleArray
     * @return GetArray
     */
    public static function get()
    {
        if(self::$get == null) self::$get = new GetArray();
        return self::$get;
    }



    /**
     * Gets/instantiates an instance of the $_POST array as an AccessibleArray
     * @return PostArray
     */
    public static function post()
    {
        if(self::$post == null) self::$post = new PostArray();
        return self::$post;
    }

    /**
     * Gets/instantiates an instance of the $_REQUEST array as an AccessibleArray
     * @return RequestArray
     */
    public static function request()
    {
        if(self::$request == null) self::$request = new RequestArray();
        return self::$request;
    }


    /**
     * Gets/instantiates an instance of the $_SERVER array as an AccessibleArray
     * @return ServerArray
     */
    public static function server()
    {
        if(self::$server == null) self::$server = new ServerArray();
        return self::$server;
    }

	/**
	 * Gets/instantiates an instance of the $_SESSION array as an AccessibleArray
	 * <strong>Note:</strong>Attempts to start a session if $_SESSION isn't set or isn't an array
	 * @return SessionArray
	 */
	public static function session()
	{
		if(self::$session == null) self::$session = new SessionArray();
		return self::$session;
	}

    /**
     * Uses the php function "getallheaders()" to fetch all of the headers and build
     * an accessible array.
     * @return HeaderArray
     */
    public static function headers()
    {
        if(self::$headers == null) self::$headers = new HeaderArray();
        return self::$headers;
    }


	///////////////////////////////////////////////////////////////
	// Strings - For handling translation and other managed messages
	///////////////////////////////////////////////////////////////

	private static $strings;

	private static function &_fetch_strings_array()
	{
		if(self::$strings == null){
			self::$strings = new AccessibleArray();
		}
		return self::$strings;
	}

	public static function strings_addStrings(ObjectAccessible $accessible)
	{
		if($accessible == null) return false;

		$util = self::util();
		$added = 0;
		$ignored = 0;
		$ar = self::_fetch_strings_array();
		foreach($accessible as $key => $value) {
			if($util->is_str($value) && ($util->is_str($key) || is_numeric($key))){
				$ar[$key] = $value;
				$added++;
			}else{
				$ignored++;
			}
		}
		return $added;
	}

	public static function strings_addString($key, $value)
	{
		$util = self::util();
		if(($util->is_str($key) || is_numeric($key)) && $util->is_str($value)){
			$ar = self::_fetch_strings_array();
			$ar[$key] = $value;
			return true;
		}
		return false;
	}

	public static function string($key)
	{
		$ar = self::_fetch_strings_array();
		return $ar->string($key);
	}

	/**
	 * Instance accessible strings
	 * @var StringAccess
	 */
	private static $string_accessor = null;
	public static function strings()
	{
		if(self::$string_accessor == null)
			self::$string_accessor = new StringAccess();
		return self::$string_accessor;
	}








}