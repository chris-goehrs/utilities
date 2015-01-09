<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 10:30 PM
 */

namespace Missilesilo\Utilities\App\Traits;

trait TraitSpecial
{
    /* ==============================================================================================
	 * START: Special
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Stuff that doesn't really fit in anywhere else right now
	 * ==============================================================================================
	 */

    /**
     * Cleans the $_POST array of quotes - use if magic quotes isn't enabled
     */
    public function clean_post_array()
    {
        array_walk_recursive($_POST, [$this, 'strip_slashes']);
    }

    /**
     * Cleans the $_REQUEST array of quotes - use if magic quotes isn't enabled
     */
    public function clean_request_array()
    {
        array_walk_recursive($_REQUEST, [$this, 'strip_slashes']);
    }

    /**
     * Cleans the $_GET array of quotes - use if magic quotes isn't enabled
     */
    public function clean_get_array()
    {
        array_walk_recursive($_GET, [$this, 'strip_slashes']);
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

    /**
     * Searches $data for any keys found in $keys
     * @param array $data - the array to be searched
     * @param array $keys - the keys to be searched for - values in order of priority
     * @return mixed (seriously, it could be anything you can store in an array)
     */
    public function getArrayValue(array &$data, array $keys)
    {
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

    public function auto_require_php_files($directory, $once = true)
    {
        $it = iterator_to_array(new \GlobIterator("{$directory}/*.php*", \GlobIterator::CURRENT_AS_PATHNAME));
        foreach($it as $file)
            if($once)
                require_once($file);
            else
                require($file);
    }

    /* ===========================================================================
     * END: Special
     * ===========================================================================
     */
}