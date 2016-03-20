<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 1/27/2016
 * Time: 2:35 PM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class DelimitedRow extends AccessibleArray
{
    public function __construct(array $columns, array $keys = array())
    {
        parent::__construct($columns);
        if($keys != null)
            $this->set_column_keys($keys);
    }

    /** @var AccessibleArray $keys */
    private $keys = null;

    /**
     * Set the column keys for the row
     *
     * @param array $keys - The keys for the index
     * @return $this
     */
    public function set_column_keys(array $keys)
    {
        if($keys == null)
            $this->keys = null;
        else
            $this->keys = new AccessibleArray($keys);

        return $this;
    }

    /**
     * Gets the keys that are available
     * @return array|null
     */
    public function get_column_keys_array()
    {
        if($this->keys == null) return null;
        return array_keys($this->keys->__toArray());
    }

    /**
     * Checks if the column exists already.
     * @param $key
     * @return bool
     */
    public function has_column_by_key($key)
    {
        if($this->keys == null) return false;
        if($this->keys->kexists($key)) return true;
        return false;
    }

    /**
     * Fetches the numeric column by the column id
     * @param $key
     * @return int|null
     */
    public function get_column_by_key($key)
    {
        if($this->keys == null) return null;
        return $this->keys->int($key);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        if($this->has_column_by_key($key)) $key = $this->get_column_by_key($key);
        return parent::set($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function un_set($key)
    {
        if($this->has_column_by_key($key)) $key = $this->get_column_by_key($key);
        return parent::un_set($key);
    }

    /**
     * @inheritDoc
     */
    public function kexists($key)
    {
        if($this->has_column_by_key($key)) $key = $this->get_column_by_key($key);
        return parent::kexists($key);
    }

    /**
     * @inheritDoc
     */
    public function raw($key)
    {
        if($this->has_column_by_key($key)) $key = $this->get_column_by_key($key);
        return parent::raw($key);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($index)
    {
        if($this->has_column_by_key($index)) $index = $this->get_column_by_key($index);
        parent::offsetExists($index);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($index)
    {
        if($this->has_column_by_key($index)) $index = $this->get_column_by_key($index);
        parent::offsetGet($index);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($index, $newval)
    {
        if($this->has_column_by_key($index)) $index = $this->get_column_by_key($index);
        parent::offsetSet($index, $newval);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($index)
    {
        if($this->has_column_by_key($index)) $index = $this->get_column_by_key($index);
        parent::offsetUnset($index);
    }

    /**
     * @inheritDoc
     */
    function __get($name)
    {
        $this->raw($name);
    }

    /**
     * @inheritDoc
     */
    function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @inheritDoc
     */
    function __unset($name)
    {
        $this->un_set($name);
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $ar = array();
        //This will make it ignore any special columns assigned
        foreach($this as $column) {
            $ar[] = $column;
        }
        return $ar;
    }


}