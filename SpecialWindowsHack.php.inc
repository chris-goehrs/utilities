<?php
//WINDOWS HACK
if(!function_exists('money_format')){
	/**
	 * 
	 * @param string $format - ignored in windows
	 * @param number $number - the number to be formatted
	 */
	function money_format($format, $number)
	{
		$locale = localeconv();
		return number_format($number, 2, $locale['decimal_point'], $locale['thousands_sep']);
	}
}