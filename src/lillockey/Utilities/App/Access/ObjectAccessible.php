<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 11/7/2015
 * Time: 3:56 PM
 */

namespace lillockey\Utilities\App\Access;


interface ObjectAccessible extends \IteratorAggregate, \ArrayAccess, \JsonSerializable, \Countable
{
	//////////////////////////////////////////////
	// Internal Access
	//////////////////////////////////////////////

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set($key, $value);

	/**
	 * @see unset
	 * @param $key
	 * @return $this
	 */
	public function un_set($key);

	/**
	 * @see array_keys
	 * @param mixed $searched_value
	 * @param boolean $strict
	 * @return array
	 */
	public function keys($searched_value = null, $strict = null);

	/**
	 * @return array - The plain values
	 */
	public function values();

	/**
	 * Does the value exist?
	 * @see array_search
	 * @param mixed   $value
	 * @param boolean $strict
	 * @return mixed
	 */
	public function exists($value, $strict = null);

	/**
	 * Does the key exist?
	 * @see array_key_exists
	 * @param mixed $key
	 * @return bool|null
	 */
	public function kexists($key);

	/**
	 * Retrieve the value raw
	 * @param $key
	 * @return mixed
	 */
	public function raw($key);

	/**
	 * Retrieve the value as an int
	 * @param $key
	 * @return int|null
	 */
	public function int($key);

	/**
	 * retrieve the value as a float
	 * @param $key
	 * @return float|null
	 */
	public function float($key);

	/**
	 * Retrieve the value as a double
	 * @param $key
	 * @return float|null
	 */
	public function double($key);

	/**
	 * Retrieve the value as a boolean
	 * @param $key
	 * @return bool
	 */
	public function boolean($key);

	/**
	 * Retrieve the value as a string
	 *
	 * @param string $key
	 * @param bool 	 $trimmed
	 * @param bool   $scrubbed
	 * @return null|string
	 */
	public function string($key, $trimmed = false, $scrubbed = false);

	/**
	 * Retrieve the value as an array
	 * @param $key
	 * @return array|null
	 */
	public function v_array($key);

	//////////////////////////////////////////////
	// Output
	//////////////////////////////////////////////
	/**
	 * @return array|null
	 */
	public function __toArray();

	/**
	 * @return mixed
	 */
	public function __toObject();

	//////////////////////////////////////////////
	// Serialization
	//////////////////////////////////////////////

	/**
	 * @return string - json encoded string
	 */
	public function jsonSerialize();

	/**
	 * @return string - serialized object
	 */
	public function serialize();

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized);

	//////////////////////////////////////////////
	// Array iteration
	//////////////////////////////////////////////
	public function getIterator();

    //////////////////////////////////////////////
    // Count
    //////////////////////////////////////////////
    public function count();

    //////////////////////////////////////////////
    // Special
    //////////////////////////////////////////////
    public function toArray();
}