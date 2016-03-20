<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/5/2015
 * Time: 5:52 PM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\App\Access\ObjectAccess\AccessibleObject;
use lillockey\Utilities\App\Access\ObjectAccessible;
use lillockey\Utilities\App\InstanceHolder;

class AccessibleArray extends \ArrayObject implements ObjectAccessible
{
	public function __construct(array $array = array())
	{
        parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
	}

    /**
     * @see array_slice
     * @param int     $offset
     * @param int     $length [optional]
     * @param boolean $preserve_keys [optional]
     * @return AccessibleArray
     */
    public function slice($offset, $length = null, $preserve_keys = null)
    {
        $ar = array_slice($this->getArrayCopy(), $offset, $length, $preserve_keys);
        return new AccessibleArray($ar);
    }

    /**
     * @see sort
     * @see rsort
     * @param bool|false $reversed
     * @return bool
     */
    public function sort($reversed = false)
    {
        $ar = (array) $this;

        if($reversed)
            return rsort($ar);

        return sort($ar);
    }

    /**
     * @see ksort
     * @see krsort
     * @param bool|false $reversed
     * @return bool
     */
    public function ksort($reversed = false)
    {
        $ar = (array) $this;

        if($reversed)
            return krsort($ar);

        return ksort($ar);
    }

    /**
     * @see usort
     * @param $function
     * @return bool
     */
    public function usort($function)
    {
        $ar = (array) $this;

        return usort($ar, $function);
    }

    /**
     * @see sizeof
     * @return int
     */
    public function size()
    {
        return sizeof($this->count());
    }

    /**
     * @see array_pop
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->array);
    }

    /**
     * @see array_push
     * @param $value
     * @return int
     */
    public function push($value)
    {
        $ar = (array) $this;
        return array_push($ar, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this[$key] = $value;
        return $this;
    }

    /**
     * @see unset
     * @param $key
     * @return $this
     */
    public function un_set($key)
    {
        unset($this[$key]);
        return $this;
    }


    /**
     * @see array_keys
     * @param mixed $searched_value
     * @param boolean $strict
     * @return array
     */
    public function keys($searched_value = null, $strict = null)
    {
        $ar = (array) $this;
        return array_keys($ar, $searched_value, $strict);
    }

    /**
     * @return array - The plain values
     */
    public function values()
    {
        $ar = (array) $this;
        return array_values($ar);
    }

    /**
     * Does the value exist?
     * @see array_search
     * @param mixed   $value
     * @param boolean $strict
     * @return mixed
     */
    public function exists($value, $strict = null)
    {
        $ar = (array) $this;
        return array_search($value, $ar, $strict);
    }

    /**
     * Does the key exist?
     * @see array_key_exists
     * @param mixed $key
     * @return bool|null
     */
    public function kexists($key)
    {
        if($key !== null) return null;
        $ar = (array) $this;
        return array_key_exists($key, $ar);
    }

    /**
     * Retrieve the value raw
     * @param $key
     * @return mixed
     */
	public function raw($key)
	{
		$util = InstanceHolder::util();
        if($key === null) {
            $val = null;
        }
		else {
            $ar = (array) $this;
            $val = $util->getArrayValue($ar, $key);
        }
        return $val;
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
        if($raw === null) return null;
        if(!is_array($raw)) return null;
        return (array) $raw;
    }

    /**
     * @return array
     */
	public function __toArray()
	{
		return (array) $this;
	}

	public function __toObject()
	{
		$ob = new AccessibleObject();
        foreach($this as $key => $value){
            $ob->set($key, $value);
        }
        return $ob;
	}

	public function jsonSerialize()
	{
        $ar = (array) $this;
		return json_encode($ar);
	}

	public function serialize()
	{
        $ar = (array) $this;
		return serialize($ar);
	}

	public function unserialize($serialized)
	{
		$unser = unserialize($serialized);
		if(is_array($unser)){
            $this->exchangeArray($unser);
        }elseif($unser instanceof AccessibleArray){
            foreach($unser as $key=>$value){
                $this->set($key, $value);
            }
        }
	}

    public function toArray()
    {
        return $this->__toArray();
    }
}