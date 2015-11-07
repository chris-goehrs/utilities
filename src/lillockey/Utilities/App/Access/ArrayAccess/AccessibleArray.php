<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 11/5/2015
 * Time: 5:52 PM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;


use lillockey\Utilities\App\Access\ObjectAccessible;
use lillockey\Utilities\App\InstanceHolder;
use Traversable;

class AccessibleArray implements ObjectAccessible
{
	private $array;

	public function __construct(array &$array = array())
	{
		$this->array = $array;
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
        $ar = array_slice($this->array, $offset, $length, $preserve_keys);
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
        if($reversed)
            return rsort($this->array);

        return sort($this->array);
    }

    /**
     * @see ksort
     * @see krsort
     * @param bool|false $reversed
     * @return bool
     */
    public function ksort($reversed = false)
    {
        if($reversed)
            return krsort($this->array);

        return ksort($this->array);
    }

    /**
     * @see usort
     * @param $function
     * @return bool
     */
    public function usort($function)
    {
        return usort($this->array, $function);
    }

    /**
     * @see sizeof
     * @return int
     */
    public function size()
    {
        return sizeof($this->array);
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
        return array_push($this->array, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        if($key === null) return $this;
        $this->array[$key] = $value;
        return $this;
    }

    /**
     * @see unset
     * @param $key
     * @return $this
     */
    public function un_set($key)
    {
        unset($this->array[$key]);
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
        return array_keys($this->array, $searched_value, $strict);
    }

    /**
     * @return array - The plain values
     */
    public function values()
    {
        return array_values($this->array);
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
        return array_search($value, $this->array, $strict);
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
        return array_key_exists($key, $this->array);
    }

    /**
     * Retrieve the value raw
     * @param $key
     * @return mixed
     */
	public function raw($key)
	{
		$util = InstanceHolder::util();
        if($key === null) return null;
		return $util->getArrayValue($this->array, $key);
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
     * @return array
     */
	public function __toArray()
	{
		return $this->array;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 *
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 *       <b>Traversable</b>
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->array);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 * @return boolean true on success or false on failure.
	 *                      </p>
	 *                      <p>
	 *                      The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return $this->exists($offset);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		$this->raw($offset);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		$this->un_set($offset);
	}

	public function __toObject()
	{
		return (object) $this->array;
	}

	public function jsonSerialize()
	{
		return json_encode($this->array);
	}

	public function serialize()
	{
		return serialize($this->array);
	}

	public function unserialize($serialized)
	{
		$unser = unserialize($serialized);
		if(is_array($unser)) $this->array = $unser;
		$this->array = (array) $unser;
	}
}