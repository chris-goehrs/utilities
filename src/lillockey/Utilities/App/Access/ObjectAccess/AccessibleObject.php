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
		$this->ob->$key = $value;
        return $this;
	}

	/**
	 * @see unset
	 * @param $key
	 * @return $this
	 */
	public function un_set($key)
	{
        unset($this->ob->$key);
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
		return isset($this->ob->$key);
	}

	/**
	 * Retrieve the value raw
	 *
	 * @param $key
	 * @return mixed
	 */
	public function raw($key)
	{
		if($this->kexists($key))
            return $this->ob->$key;
        return null;
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
	 *
	 * @param string $key
	 * @param bool 	 $trimmed
	 * @param bool   $scrubbed
	 * @return null|string
	 */
	public function string($key, $trimmed = false, $scrubbed = false)
	{
		$raw = $this->raw($key);
		if($raw === null) return null;

		$trimmed = $trimmed ? true : false;
		$scrubbed = $scrubbed ? true : false;
		$str = strval($raw);

		if($trimmed) $str = trim($str);
		if($scrubbed) $str = htmlentities($str, ENT_QUOTES);

		return $str;
	}


	/**
	 * Retrieve the value as an array
	 * @param $key
	 * @return array|null
	 */
	public function v_array($key)
	{
		$raw = $this->raw($key);
		if($raw === null) return $value = null;
		if(!is_array($raw)) return $value = null;
		return $value = (array) $raw;
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
            if(is_object($unser)){
                $this->ob = $unser;
            }elseif(is_array($unser)){
                $o = new \stdClass();
                foreach($unser as $key => $value){
                    $o->$key = $value;
                }
                $this->ob = $o;
            }

		}
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->__toArray());
	}

	public function offsetExists($offset)
	{
		return $this->kexists($offset);
	}

	public function offsetGet($offset)
	{
		return $val = $this->raw($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->un_set($offset);
	}

    public function __get($name)
    {
        return $this->raw($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->kexists($name);
    }

    public function __unset($name)
    {
        return $this->un_set($name);
    }

    public function count()
    {
        return count($this->__toArray());
    }

    public function toArray()
    {
        $this->__toArray();
    }
}