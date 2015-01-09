<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 10:19 PM
 */

namespace Missilesilo\Utilities\App\Traits;

define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST', 1);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST', 2);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST', 3);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST', 4);

define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY', 'alpha');
define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC', 'alphanum');
define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL', 'alphanumspec');

trait TraitString
{
    /* ===========================================================================
	 * START: String helpers
	 * Inserted By: Christopher Goehrs 5/31/2014
	 * ===========================================================================
	 */

    private function _deep_replace($search, $subject)
    {
        $subject = (string) $subject;

        $count = 1;
        while ( $count ) {
            $subject = str_replace( $search, '', $subject, $count );
        }

        return $subject;
    }

    /**
     * Checks to see if a string starts with another string
     * @param string $str_subject - the string to check
     * @param string $str_is - does $str_subject start with this?
     * @param boolean $ignore_case
     * @return boolean - true if $str_subject starts with $str_is, false if otherwise
     */
    public function str_left_is($str_subject, $str_is, $ignore_case = false)
    {
        //Basic assumptions
        if($str_subject === $str_is) return true;
        if($str_subject === null && $str_subject === null) return true;
        if($str_subject === null) return false;
        if(strlen($str_subject) === strlen($str_is) && strlen($str_subject) == 0) return true;
        if(strlen($str_subject) < strlen($str_is)) return false;

        //Check for ignored case request
        if($ignore_case === true){
            $str_subject = strtolower($str_subject);
            $str_is = strtolower($str_is);
        }

        return substr($str_subject, 0, strlen($str_is)) === $str_is;
    }

    /**
     * Checks to see if a string starts with another string
     * @param string $str_subject - the string to check
     * @param string $str_is - does $str_subject start with this?
     * @param boolean $ignore_case
     * @return boolean - true if $str_subject starts with $str_is, false if otherwise
     */
    public function str_right_is($str_subject, $str_is, $ignore_case = false)
    {
        //Basic assumptions
        if($str_subject === $str_is) return true;
        if($str_subject === null && $str_subject === null) return true;
        if($str_subject === null) return false;
        if(strlen($str_subject) === strlen($str_is) && strlen($str_subject) == 0) return true;
        if(strlen($str_subject) < strlen($str_is)) return false;

        //Check for ignored case request
        if($ignore_case === true){
            $str_subject = strtolower($str_subject);
            $str_is = strtolower($str_is);
        }

        $start_location = strlen($str_subject) - strlen($str_is);
        $search_length = strlen($str_is);
        $str_subject_contents = substr($str_subject, $start_location, $search_length);

        return $str_subject_contents == $str_is;
    }

    /**
     * Generates a randomized string
     * @author Christopher R. Goehrs
     * @since 5/31/2014
     * @param integer $length
     * @param string $type - can be any of the following values<ul>
     * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY</li>
     * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC</li>
     * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL</li></ul>
     * @return NULL|string null if invalid type or length isn't a valid integer / otherwise random string
     */
    public function random_string($length, $type=MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL){
        $cur = 0;
        $str = '';

        if(!is_int($length)) return null;

        //Set up the character set
        $chars = null;
        if($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY)
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        elseif($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC)
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        elseif($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL)
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-{}[]?/>.<,`~|\\';
        if($chars == null) return null;

        //Calculate the random character floor and ceiling values
        $chars_floor = 0;
        $chars_ceiling = strlen($chars) - 1;

        //Seed the randomizer
        srand(time());
        while($cur < $length){
            //Select and add the random character to the string
            $str .= $chars[rand($chars_floor, $chars_ceiling)];
            $cur++;
        }

        return $str;
    }

    /**
     * Evaluates if the value provided is a string or can be evaluated as a string
     * @param $value
     * @return bool true if it can / false if it cannot
     */
    public function is_str($value)
    {
        return !is_array($value) &&
        (
            (!is_object( $value ) && settype( $value, 'string' ) !== false) ||
            (is_object($value) && method_exists($value, '__toString'))
        );
    }

    /**
     * Checks if a string is currently serialized
     * @param string $str
     * @return boolean true if serialized | false otherwise
     */
    public function isSerialized($str) {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }

    /**
     * Strips slashes by reference in the given string
     * @param $value
     */
    protected function strip_slashes(&$value)
    {
        if($this->is_str($value))
            $value = stripslashes($value);
        //else
        //do nothing (implied)
    }

    /**
     * It, um, masks a string?  The default values are set up for masking a credit card number
     * @param string $subject - the string to be masked
     * @param string $mask - a single character used to mask the string
     * @param int $mask_length_value - this is the length as it relates to the mask type
     * 		for "all_but" mask types, this represents what remains visible
     * 		fir "first"/"last" mask types, this represents the amount that is being covered up
     * @param int $mask_type - must be one of the following:
     * 	<ul><li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST</li>
     * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST</li>
     * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST</li>
     * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST</li></ul>
     * @return string - The masked string
     */
    public function mask_string($subject, $mask = '*', $mask_length_value = 4, $mask_type = MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST)
    {
        if(!is_int($mask_length_value)) return $subject;
        if(!$this->is_str($subject)) return $subject;
        if(!$this->is_str($mask)) return $subject;
        if(!is_int($mask_type)) return $subject;
        if(strlen($mask) > 1) $mask  = substr($mask, 0, 1);

        $slen = strlen($subject);

        switch($mask_type){
            case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST:
                $remaining_visible = $slen - $mask_length_value;
                if($remaining_visible < 1) return str_pad('', $slen, $mask);

                return str_pad('', $mask_length_value, $mask).$this->str_right($subject, $remaining_visible);
                break;
            case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST:
                $remaining_visible = $slen - $mask_length_value;
                if($remaining_visible < 1) return str_pad('', $slen, $mask);

                return $this->str_left($subject, $remaining_visible).str_pad('', $mask_length_value, $mask);
                break;
            case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST:
                $mask_length = $slen - $mask_length_value;
                if($mask_length < 1) return $subject;

                return $this->str_left($subject, $mask_length_value).str_pad('', $mask_length, $mask);
                break;
            case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST:
                $mask_length = $slen - $mask_length_value;
                if($mask_length < 1) return $subject;

                return str_pad('', $mask_length, $mask) . $this->str_right($subject, $mask_length_value);
                break;
        }

        return $subject;
    }

    public function str_right($subject, $length)
    {
        if(!$this->is_str($subject)) return null;
        if(!is_int($length)) return null;
        if($length > strlen($subject)) return $subject;

        return substr($subject, strlen($subject) - $length, $length);
    }

    public function str_left($subject, $length)
    {
        if(!$this->is_str($subject)) return null;
        if(!is_int($length)) return null;
        if($length > strlen($subject)) return $subject;

        return substr($subject, 0, $length);
    }

    /* ===========================================================================
     * END: String helpers
     * ===========================================================================
     */
}