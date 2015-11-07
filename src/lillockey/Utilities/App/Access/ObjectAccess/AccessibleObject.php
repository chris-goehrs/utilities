<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 11/7/2015
 * Time: 1:43 PM
 */

namespace lillockey\Utilities\App\Access\ObjectAccess;


use lillockey\Utilities\App\Access\ObjectAccessible;

class AccessibleObject implements ObjectAccessible
{
	private $ob;
	private $is_magical = false;

	public function __construct(&$object = null)
	{
		if($object == null){
			$this->ob = new \stdClass();
		}elseif(is_array($object)){
			$this->_set_values_from_array($object);
		}elseif(is_object($object)){
			$this->ob = $object;
		}else{
			$this->ob = new \stdClass();
		}

		if(method_exists($this->ob, '__set')){
			$this->is_magical = true;
		}
	}

	private function _set_values_from_array(array &$array)
	{
		$this->ob = new \stdClass();
		foreach($array as $key=>$value)
			$this->ob->$key = $value;
	}






	/**
	 * is utilized for reading data from inaccessible members.
	 *
	 * @param $name string
	 * @return mixed
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
	 */
	function __get($name)
	{
		if($this->__isset($name)){
			return $this->ob->$name;
		}else{
			return null;
		}
	}

	/**
	 * run when writing data to inaccessible members.
	 *
	 * @param $name  string
	 * @param $value mixed
	 * @return void
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
	 */
	function __set($name, $value)
	{
		if($this->is_magical){
			$this->ob->$name = $value;
		}
	}

	/**
	 * is triggered by calling isset() or empty() on inaccessible members.
	 *
	 * @param $name string
	 * @return bool
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
	 */
	function __isset($name)
	{
		return isset($this->ob->$name);
	}

	/**
	 * is invoked when unset() is used on inaccessible members.
	 *
	 * @param $name string
	 * @return void
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
	 */
	function __unset($name)
	{
		if($this->__isset($name))
			unset($this->ob->$name);
	}

	/**
	 * The __toString method allows a class to decide how it will react when it is converted to a string.
	 *
	 * @return string
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 */
	function __toString()
	{
		return (string) $this->ob;
	}


	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set($key, $value)
	{
		$this->__set($key, $value);
		return $this;
	}

	/**
	 * @see unset
	 * @param $key
	 * @return $this
	 */
	public function un_set($key)
	{
		$this->__unset($key);
		return $this;
	}

	/**
	 * @see array_keys
	 * @param mixed   $searched_value
	 * @param boolean $strict
	 * @return array
	 */
	public function keys($searched_value = null, $strict = null)
	{
		$ar = $this->__toArray();
		return array_keys($ar, $searched_value, $strict);
	}

	/**
	 * @return array - The plain values
	 */
	public function values()
	{
		$ar = $this->__toArray();
		$out = array();
		foreach($ar as $value) $out[] = $value;
		return $out;
	}

	/**
	 * Does the value exist?
	 *
	 * @see array_search
	 * @param mixed   $value
	 * @param boolean $strict
	 * @return mixed
	 */
	public function exists($value, $strict = null)
	{
		$ar = $this->__toArray();
		return array_search($value, $ar, $strict);
	}

	/**
	 * Does the key exist?
	 *
	 * @see array_key_exists
	 * @param mixed $key
	 * @return bool|null
	 */
	public function kexists($key)
	{
		return $this->__isset($key);
	}

	/**
	 * Retrieve the value raw
	 *
	 * @param $key
	 * @return mixed
	 */
	public function raw($key)
	{
		return $this->__get($key);
	}

	/**
	 * Retrieve the value as an int
	 * @param $key
	 * @return int|null
	 */
	public function int($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;
		return intval($raw);
	}

	/**
	 * retrieve the value as a float
	 * @param $key
	 * @return float|null
	 */
	public function float($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;
		return floatval($raw);
	}

	/**
	 * Retrieve the value as a double
	 * @param $key
	 * @return float|null
	 */
	public function double($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;
		return doubleval($raw);
	}

	/**
	 * Retrieve the value as a boolean
	 * @param $key
	 * @return bool
	 */
	public function boolean($key)
	{
		return $this->raw($key) ? true : false;
	}

	/**
	 * Retrieve the value as a string
	 * @param $key
	 * @return null|string
	 */
	public function string($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;
		return strval($raw);
	}


	/**
	 * Retrieve the value as an array
	 * @param $key
	 * @return array|null
	 */
	public function v_array($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;
		if(!is_array($raw)) return null;
		return (array) $raw;
	}

	/**
	 * @return array|null
	 */
	public function __toArray()
	{
		return get_object_vars($this->ob);
	}

	/**
	 * @return mixed
	 */
	public function __toObject()
	{
		return $this->ob;
	}

	/**
	 * @return string - json encoded string
	 */
	public function jsonSerialize()
	{
		return json_encode($this->ob);
	}

	/**
	 * @return string - serialized object
	 */
	public function serialize()
	{
		return serialize($this->ob);
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		$unser = unserialize($serialized);
		if($unser != null){
			$this->ob = $unser;
		}
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->__toArray());
	}

	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	public function offsetGet($offset)
	{
		return $this->raw($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->un_set($offset);
	}
}