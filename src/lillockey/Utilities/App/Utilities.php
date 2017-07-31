<?php

namespace lillockey\Utilities\App;

use lillockey\Utilities\App\Containers\FileInformationResults;
use lillockey\Utilities\Config\AbstractCustomConfig;
use lillockey\Utilities\Exceptions\NotAnArrayException;

//================================================
// Section * - Some basic constants
//================================================

define('LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH', false);
define('LILLOCKEY_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET', true);

//================================================
// Section 3 - String Manipulation Constants
//================================================

define('LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__FIRST', 1);
define('LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__LAST', 2);
define('LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST', 3);
define('LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST', 4);

define('LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY', 'alpha');
define('LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC', 'alphanum');
define('LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL', 'alphanumspec');

/**
 * Class Utilities
 *
 * @property AbstractCustomConfig config
 * @package lillockey\Utilities\App
 */
class Utilities
{
	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 1
	//      String Manipulation
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

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
	 * <li>LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY</li>
	 * <li>LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC</li>
	 * <li>LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL</li></ul>
	 * @return NULL|string null if invalid type or length isn't a valid integer / otherwise random string
	 */
	public function random_string($length, $type=LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL){
		$cur = 0;
		$str = '';

		if(!is_int($length)) return null;

		//Set up the character set
		$chars = null;
		if($type == LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY)
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		elseif($type == LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC)
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		elseif($type == LILLOCKEY_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL)
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
	public function is_str(&$value)
	{
		return !is_array($value) &&
		(
			(!is_object( $value ) && settype( $value, 'string' ) !== false) ||
			(is_object($value) && method_exists($value, '__toString'))
		);
	}

	/**
	 * Evaluates to see if the given value is a valid json string
	 *
	 * <p>
	 * <strong>NOTE</strong>:
	 * This is based on the answer found <a href="http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php">here</a>
     * The results are not infallible.  False negatives have popped up every once in a while.
	 * </p>
	 *
	 * @param $value
	 * @return bool
	 */
	public function is_json(&$value)
	{
		//If it's null, of course it's not json (derp)
		if($value == null) return false;

		//If it can't be interpereted as a string, it's not json.
		if(!$this->is_str($value)) return false;

		//If it doesn't contain either "{" or "[", it might evaluate as okay but not actually be a json object
		if(strpos($value, '{') === false && strpos($value, '[') === false) return false;

		return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
			preg_replace('/"(\\.|[^"\\\\])*"/', '', $value));
	}

	/**
	 * Checks if a string is currently serialized
	 * @param string $str
	 * @return boolean true if serialized | false otherwise
	 */
	public function is_serialized($str) {
		return ($str == serialize(false) || @unserialize($str) !== false);
	}

	/**
	 * Strips slashes by reference in the given string
	 * @param $value
	 */
	public function strip_slashes(&$value)
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
	 * 	<ul><li>LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__FIRST</li>
	 * 		<li>LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__LAST</li>
	 * 		<li>LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST</li>
	 * 		<li>LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST</li></ul>
	 * @return string - The masked string
	 */
	public function mask_string($subject, $mask = '*', $mask_length_value = 4, $mask_type = LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST)
	{
		if(!is_int($mask_length_value)) return $subject;
		if(!$this->is_str($subject)) return $subject;
		if(!$this->is_str($mask)) return $subject;
		if(!is_int($mask_type)) return $subject;
		if(strlen($mask) > 1) $mask  = substr($mask, 0, 1);

		$slen = strlen($subject);

		switch($mask_type){
			case LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__FIRST:
				$remaining_visible = $slen - $mask_length_value;
				if($remaining_visible < 1) return str_pad('', $slen, $mask);

				return str_pad('', $mask_length_value, $mask).$this->str_right($subject, $remaining_visible);
				break;
			case LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__LAST:
				$remaining_visible = $slen - $mask_length_value;
				if($remaining_visible < 1) return str_pad('', $slen, $mask);

				return $this->str_left($subject, $remaining_visible).str_pad('', $mask_length_value, $mask);
				break;
			case LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST:
				$mask_length = $slen - $mask_length_value;
				if($mask_length < 1) return $subject;

				return $this->str_left($subject, $mask_length_value).str_pad('', $mask_length, $mask);
				break;
			case LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST:
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

	public function pad_string_left($subject, $pad_using, $length)
	{
		return str_pad($subject, $length, $pad_using, STR_PAD_LEFT);
	}

	public function pad_string_right($subject, $pad_using, $length)
	{
		return str_pad($subject, $length, $pad_using, STR_PAD_RIGHT);
	}

	public function pad_string_both($subject, $pad_using, $length)
	{
		return str_pad($subject, $length, $pad_using, STR_PAD_BOTH);
	}

	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 2
	//      URL Construction & Parsing
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * Get the base url for this request
	 * @param string $append_to_base (anything set to immediately follow the base url)
	 * @return string
	 */
	public function base_url($append_to_base = '')
	{
		$str = $this->server('REQUEST_SCHEME').'://'.$this->server(array('HTTP_HOST', 'SERVER_NAME')).(strlen($append_to_base)?'/'.rawurldecode($append_to_base):'');
		return $str;
	}

	/**
	 * @param $base_url
	 * @param int $status
	 * @param array $subids
	 * @return bool
	 */
	public function redirect_to_url($base_url, $status = 302, array $subids = array())
	{
		$sanitized_url = $this->build_and_sanitize_url($base_url, $subids);
		$header = "Location: $sanitized_url";
		header($header, true, $status);
		return true;
	}

	/**
	 * @param $url
	 * @param int $status
	 * @return bool
	 */
	public function redirect_to_url_raw($url, $status = 302)
	{
		$header = "Location: $url";
		header($header, true, $status);
		return true;
	}

	/**
	 * @param int $status
	 * @return bool
	 */
	public function redirect_to_base_url($status = 302)
	{
		return $this->redirect_to_url_raw($this->base_url(), $status);
	}

	/**
	 * Constructs a sanitized url from a base url and an associative array
	 * @param $base_url
	 * @param array $subids
	 * @return string
	 */
	public function build_and_sanitize_url($base_url, array $subids = array())
	{
		return $this->sanitize_url($base_url.(sizeof($subids)?'?'.http_build_query($subids):''));
	}

	/**
	 * @return string
	 * @throws NotAnArrayException
	 */
	public function get_current_domain()
	{
		return $this->server(array('HTTP_HOST', 'SERVER_NAME'));
	}

	/**
	 * Sanitizes the url
	 * @param $url
	 * @return string
	 */
	public function sanitize_url($url)
	{
		$url = (string) $url;
		$url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $url);
		$url = preg_replace('/\0+/', '', $url);
		$url = preg_replace('/(\\\\0)+/', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');

		$count = 1;
		while ( $count ) {
			$url = str_replace( $strip, '', $url, $count );
		}

		$url = $this->_deep_replace($strip, $url);
		return $url;
	}

	/**
	 * Convenience method, really.  Checks if any headers have already been sent ... just like its namesake.
	 * @return boolean
	 */
	public function headers_sent()
	{
		return headers_sent();
	}

	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 3
	//      Request reading functions
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * Checks to see if the current request is a post
	 * @return boolean
	 */
	public function request_is_post()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	/**
	 * Retrieves the refering domain from $_SERVER['HTTP_REFERER']
	 * @return string
	 */
	public function refering_domain()
	{
		return parse_url($this->referer(), PHP_URL_HOST);
	}

	/**
	 * Convenience method for
	 * @return string
	 */
	public function referer()
	{
		return $this->server('HTTP_REFERER');
	}

	/**
	 * Searches the $_SERVER variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function server($keys, $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_SERVER)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SERVER is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_SERVER, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_SERVER, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}

	/**
	 * Searches the $_GET variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function get($keys, $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_GET)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_GET is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_GET, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_GET, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}

	public function request($keys, $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_REQUEST)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_GET is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_REQUEST, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_REQUEST, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}



	/**
	 * Searches the $_POST variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function post($keys, $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_POST)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_POST is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_POST, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_POST, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}


	/**
	 * Searches $_SESSION for the given key(s)
	 * @param array/string/object $keys
	 * @param boolean $run_session_start - Run session_start() before retrieval?
	 * @param boolean $throw_exception_when_cant_be_searched - throw exception when unsearchable?
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function session(
		$keys,
		$run_session_start = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET,
		$throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if($run_session_start && !isset($_SESSION))
			session_start();

		if(!is_array($_SESSION)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SESSION is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_SESSION, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_SESSION, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}

	public function session_unset($key,
	                              $run_session_start = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET,
	                              $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if($run_session_start && !isset($_SESSION))
			session_start();

		if(!is_array($_SESSION)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SESSION is not an array');
			return $this;
		}

		unset($_SESSION[$key]);
		return $this;
	}

	public function session_set($key, $value,
	                            $run_session_start = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET,
	                            $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if($run_session_start && !isset($_SESSION))
			session_start();

		if(!is_array($_SESSION)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SESSION is not an array');
			return $this;
		}

		$_SESSION[$key] = $value;
		return $this;
	}

	/**
	 * Searches the $_COOKIE variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws \Exception
	 * @return mixed - the value | null
	 */
	public function cookie($keys, $throw_exception_when_cant_be_searched = LILLOCKEY_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_COOKIE)){
			if($throw_exception_when_cant_be_searched) throw new \Exception('$_COOKIE is not an array');
			return null;
		}

		if($this->is_str($keys))
			return $this->getArrayValue($_COOKIE, array($keys));
		else{
			if(is_array($keys))
				return $this->getArrayValue($_COOKIE, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new \Exception('$keys is neither a string nor an array');
				return null;
			}
		}
	}


	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 4
	//      Validation
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * Checks to see if the provided email address is valid
	 * @param string $email - the email address to be validated
	 * @param boolean $check_mx - (optional) true will attempt to validate the DNS records for the given email address
	 * @return boolean - true if valid/false if invalid
	 */
	public function validate_email($email, $check_mx = true)
	{
		$sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$email_as_address_is_okay = $sanitized_email?true:false;
		if(!$check_mx || !$email_as_address_is_okay) return $email_as_address_is_okay;

		list($user, $domain) = explode('@', $sanitized_email);
		return checkdnsrr($domain, 'MX');
	}

	/**
	 * Validates the credit card number using the Luhn algorithm and a null checker
	 *
	 * Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
	 * This code has been released into the public domain, however please      *
	 * give credit to the original author where possible.                      *
	 *
	 * @param string $number - The credit card number to be validated
	 * @return boolean true if okay/false if not
	 */
	public function validate_credit_card_number($number)
	{
		if(strlen($number) < 7) return false;

		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number=preg_replace('/\D/', '', $number);

		// Set the string length and parity
		$number_length=strlen($number);
		$parity=$number_length % 2;

		// Loop through each digit and do the maths
		$total=0;
		for ($i=0; $i<$number_length; $i++) {
			$digit=$number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit*=2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit-=9;
				}
			}
			// Total up the digits
			$total+=$digit;
		}

		// If the total mod 10 equals 0, the number is valid
		$is_valid = ($total % 10 == 0) ? TRUE : FALSE;
		return $is_valid;
	}

	/**
	 * Validates a credit card expiration month and year. <br/>
	 * The expiration is considered valid when it is both in the future and within 10 years of the current date
	 *
	 * @param string $month - MM
	 * @param string $year - YYYY
	 * @return boolean true if valid/false if not
	 */
	public function validate_credit_card_expiration($month, $year)
	{
		//Grab the expiration time
		$exp_ts = mktime(0, 0, 0, ($month + 1), 1, $year);
		$exp_ts = strtotime("+1 month", $exp_ts);   //Validates against the first second of the month following the expiration

		//Grab the current time
		$cur_ts = time();

		// Don't validate for dates more than 10 years in future.
		$max_ts = $cur_ts + (10 * 365 * 24 * 60 * 60);

		//Validate the date
		return $exp_ts > $cur_ts && $exp_ts < $max_ts;
	}

	/**
	 * Validates a credit card CVV.<br/>
	 * The CVV is valid if the credit card is AmEx and the cvv is 4 digits OR<br/>
	 *      if the credit card is Visa, MC, or Discover and the cvv is 3 digits
	 *
	 * NOTE: Thanks to RichardH over at authorize.net
	 * http://community.developer.authorize.net/t5/The-Authorize-Net-Developer-Blog/Validating-Credit-Card-Information-Part-3-of-3-CVV-Numbers/ba-p/7657
	 *
	 * @param $card_number - The credit card number for the CVV
	 * @param $cvv - The CVV to be validated
	 * @return bool
	 */
	public function validate_credit_card_cvv($card_number, $cvv)
	{
		$first_number = (int) substr(trim($card_number), 0, 1);
		if($first_number === 3){
			if(strlen($cvv) !== 4) {
				// The credit card is an American Express card but does not have a four digit CVV code
				return false;
			}
		}elseif(strlen($cvv) !== 3) {
			// The credit card is a Visa, MasterCard, or Discover Card card but does not have a three digit CVV code
			return false;
		}
		return true;
	}

	/**
	 * Validates a phone number
	 *
	 * @param string $number - The phone number to validate
	 * @param boolean $advanced_validation - Checks the format of the number
	 * @return boolean true if valid/false if not
	 */
	public function validate_phone_number($number, $advanced_validation = false)
	{
		$number = str_replace('-', '', $number);
		$number = str_replace('+', '', $number);
		$number = str_replace('(', '', $number);
		$number = str_replace(')', '', $number);
		$number = str_replace('.', '', $number);
		$number = str_replace(',', '', $number);
		$number = str_replace(' ', '', $number);
		$number = trim($number);

		if(strlen($number) < 3){
			return false;
		}

		if($advanced_validation == false){
			return true;
		}

		$regex = "/^((\+|00)\d{1,3})?\d+$/";
		return (preg_match( $regex, $number )?true:false);
	}

	/**
	 * Validates the given date and ensures that it is at least a certain number of years ago
	 * @param int $mm - Birth month (1-12)
	 * @param int $dd - Birth day (1-31)
	 * @param int $yyyy - Birth year (1-32767)
	 * @param int $at_least_x_years_ago [optional] - the number of years in the past this date should be<br/>
	 *              In order to be processed, it must be an integer greater than 0
	 * @return bool
	 */
	public function validate_birth_date($mm, $dd, $yyyy, $at_least_x_years_ago = 0)
	{
		//Make sure it's a valid Gregorian date
		$its_a_date = checkdate($mm, $dd, $yyyy);

		//If it's not, let's forget the rest and move on
		if(!$its_a_date) return 'not a real date';

		//Should we check for the number of years in the past this day ought to be?
		if(is_int($at_least_x_years_ago) && $at_least_x_years_ago > 0){
			//We should?  Here we go ...
			$past_date = strtotime("-{$at_least_x_years_ago} year", time());
			$listed_date = strtotime("$yyyy-$mm-$dd");
			return $listed_date <= $past_date;
		}else{
			//No?  Well then I guess everything is fine
			return true;
		}
	}

	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 5
	//      Flavor
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////


	/**
	 * Sets up the headers for the given content type
	 * @param $mime - the mime-type for the data provided - e.g. application/json
	 * @param $data
	 * @param null $attachment_filename
	 */
	public function flavor_echo($mime, $data, $attachment_filename = null)
	{
		header("Content-Type: $mime");
		header("Content-length: ".strlen($data));
		if($attachment_filename != null){
			header("Content-disposition: attachment; filename=\"$attachment_filename\"");
		}
		echo $data;
		die;
	}

	/**
	 * Encodes $data and echos it with some pre-fabricated headers - convenience method
	 * @param $data - raw data to be encoded using json_encode
	 */
	public function flavor_echo_json($data)
	{
		$this->flavor_echo('application/json', json_encode($data));
	}




	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 6
	//      Special
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * Calculate everything regarding pagination
	 * @param $entries_per_page
	 * @param $current_page
	 * @param $total_entries
	 * @return array
	 */
	function calculate_pagination($entries_per_page, $current_page, $total_entries)
	{
		//Calculate the current offset & total number of pages
		$offset = ($current_page - 1) * $entries_per_page;
		$divides_nicely = ($total_entries % $entries_per_page) == 0;
		$total_pages = $total_entries / $entries_per_page;
		if(!$divides_nicely) $total_pages++;
		$total_pages = intval($total_pages);

		$pages = array();

		//Build the individual pages
		for($i = 1; $i <= $total_pages ; $i++ ){
			$pages['pages'][$i]['index'] = $i;
			if($i == $current_page){
				$pages['pages'][$i]['enabled'] = false;
				$pages['pages'][$i]['bootstrap'] = 'active';
			}else{
				$pages['pages'][$i]['enabled'] = true;
				$pages['pages'][$i]['bootstrap'] = '';
			}
		}

		//Previous
		$pages['prev']['enabled'] = $current_page > 1;
		$pages['prev']['bootstrap'] = $current_page > 1 ? '' : 'disabled';
		$pages['prev']['index'] = $current_page - 1;

		//Next
		$pages['next']['enabled'] = $current_page < $total_pages;
		$pages['next']['bootstrap'] = $current_page < $total_pages ? '' : 'disabled';
		$pages['next']['index'] = $current_page + 1;

		return array(
			'offset' => $offset,
			'total_pages' => $total_pages,
			'page_data' => $pages
		);
	}

	/**
	 * Cleans the $_POST array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_post_array()
	{
		array_walk_recursive($_POST, array($this, 'strip_slashes'));
	}

	/**
	 * Cleans the $_REQUEST array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_request_array()
	{
		array_walk_recursive($_REQUEST, array($this, 'strip_slashes'));
	}

	/**
	 * Cleans the $_GET array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_get_array()
	{
		array_walk_recursive($_GET, array($this, 'strip_slashes'));
	}

	/**
	 * Gets just the username of a valid email address
	 * @param string $email
	 * @return boolean|string
	 */
	public function get_email_username($email)
	{
		$sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$email_as_address_is_okay = $sanitized_email?true:false;
		if(!$email_as_address_is_okay) return false;

		list($user, $domain) = explode('@', $sanitized_email);
		return $user;
	}

	public function get_email_domain($email)
	{
		$sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$email_as_address_is_okay = $sanitized_email?true:false;
		if(!$email_as_address_is_okay) return false;

		list($user, $domain) = explode('@', $sanitized_email);
		return $domain;
	}

	/**
	 * Searches $data for any keys found in $keys.  $keys can be a regular array key or an array of keys (whose values are searched for in order)
	 * @param array 	$data - the array to be searched
	 * @param mixed 	$keys - the keys to be searched for - values in order of priority
	 * @return mixed 	(seriously, it could be anything you can store in an array)
	 */
	public function getArrayValue(array &$data, $keys)
	{
		if(!is_array($keys)) $keys = array($keys);
		if(!sizeof($keys)) return null;
		if(!is_array($data)) return null;
		$top_key = array_shift($keys);

		if(array_key_exists($top_key, $data))
		{
			return $data[$top_key];
		}else{
			return (sizeof($keys)?$this->getArrayValue($data, $keys):null);
		}
	}

	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 7
	//      File/Data Information
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * An array of known mime types and their default extensions
	 *
	 * NOTE: This is not intended to be comprehensive on any level.
	 * Rather, it is intended to be a simple list for processing some
	 * basic file types that are encountered during upload/storage.
	 * @var array
	 */
	private $mime_to_ext = array(
		// Images
		'image/jpg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'image/gif' => 'gif',
	);

	/**
	 * Returns the extension for the mime type given.  If none is found, null is returned
	 * @param $mime_type
	 * @return mixed
	 */
	public function provide_recommended_file_extension($mime_type)
	{
		return $this->getArrayValue($this->mime_to_ext, $mime_type);
	}

	/**
	 * @param $file_path - The location of the file to retrieve information against
	 * @return FileInformationResults
	 * @throws \Exception
	 */
	public function get_file_information($file_path)
	{
		if(!InstanceHolder::fileinfo_enabled())
			throw new \Exception("FileInfo extension not available");

		//Grab the mime type and encoding
		$finfo = finfo_open(FILEINFO_MIME);
		$full_mime = finfo_file($finfo, $file_path);
		$mimex = explode(";", $full_mime);
		$type = $this->getArrayValue($mimex, 0);
		$full_encode = $this->getArrayValue($mimex, 1);
		$full_encode = $full_encode === null ? '' : $full_encode;
		$encodex = explode('=', $full_encode);
		$encoding = $this->getArrayValue($encodex, 1);

		//Grab the file size
		$size = filesize($file_path);

		//Grab the recommended extension
		$recommended_extension = $this->provide_recommended_file_extension($type);

		return new FileInformationResults($type, $encoding, $recommended_extension, $size);
	}

	/**
	 * @param $data - the raw data to check
	 * @return FileInformationResults
	 * @throws \Exception
	 */
	public function get_data_information(&$data)
	{
		if(!InstanceHolder::fileinfo_enabled())
			throw new \Exception("FileInfo extension not available");

		//Grab the mime type and encoding
		$finfo = finfo_open(FILEINFO_MIME);
		$full_mime = finfo_buffer($finfo, $data);
		$mimex = explode(";", $full_mime);
		$type = $this->getArrayValue($mimex, 0);
		$full_encode = $this->getArrayValue($mimex, 1);
		$full_encode = $full_encode === null ? '' : $full_encode;
		$encodex = explode('=', $full_encode);
		$encoding = $this->getArrayValue($encodex, 1);

		//Grab the file size
		$size = strlen($data);

		//Grab the recommended extension
		$recommended_extension = $this->provide_recommended_file_extension($type);

		return new FileInformationResults($type, $encoding, $recommended_extension, $size);
	}
}   //END: Class Utilities
