<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/24/2015
 * Time: 5:43 PM
 */

namespace lillockey\Utilities\App\Helper;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class StringComparableArray extends AccessibleArray
{
	/**
	 * Purge ALL items that are not an instance of StringComparable<br/>
	 * <strong>Note:</strong> This is run before a sort is run
	 * @return $this
	 */
	public function purge_non_string_comparable()
	{
		$rmk = array();

		foreach($this as $key => $value){
			if(!($value instanceof StringComparable)){
				$rmk[] = $key;
			}
		}

		foreach($rmk as $key){
			$this->un_set($key);
		}

		return $this;
	}

	public function sort_string($maintain_key_association = false)
	{
		//First purge non StringComparable stuff
		$this->purge_non_string_comparable();

		if($maintain_key_association) {
			uasort($this->array, array($this, 'compare'));
		} else {
			usort($this->array, array($this, 'compare'));
		}
	}

	/**
	 * Compare the two string values associated with StringComparable
	 * @param StringComparable $val1
	 * @param StringComparable $val2
	 * @return int
	 */
	public function compare(StringComparable &$val1, StringComparable &$val2)
	{
		if($val1 == null) return 0;
		if($val2 == null) return 0;

		return strcmp($val1->compare_get_string(), $val2->compare_get_string());
	}
}