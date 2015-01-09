<?php

namespace Missilesilo\Utilities\App\Traits;
use Missilesilo\Utilities\Exceptions\NotAnArrayException;

/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 10:27 PM
 */

trait TraitRequest
{
    /* ==============================================================================================
	 * START: HTTP request information
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Checks various server and request information
	 * ==============================================================================================
	 */

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
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Searches the $_SERVER variable for the keys
     * @param array/string/object $keys
     * @param boolean $throw_exception_when_cant_be_searched
     * @throws NotAnArrayException
     * @return string - the value | null
     */
    public function server($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
    {
        if(!is_array($_SERVER)){
            if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SERVER is not an array');
            return null;
        }

        if($this->is_str($keys))
            return $this->getArrayValue($_SERVER, [$keys]);
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
    public function get($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
    {
        if(!is_array($_GET)){
            if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_GET is not an array');
            return null;
        }

        if($this->is_str($keys))
            return $this->getArrayValue($_GET, [$keys]);
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

    /**
     * Searches the $_POST variable for the keys
     * @param array/string/object $keys
     * @param boolean $throw_exception_when_cant_be_searched
     * @throws NotAnArrayException
     * @return string - the value | null
     */
    public function post($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
    {
        if(!is_array($_POST)){
            if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_POST is not an array');
            return null;
        }

        if($this->is_str($keys))
            return $this->getArrayValue($_POST, [$keys]);
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
        $run_session_start = MISSILESILO_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET,
        $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
    {
        if($run_session_start)
            session_start();

        if(!is_array($_SESSION)){
            if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SESSION is not an array');
            return null;
        }

        if($this->is_str($keys))
            return $this->getArrayValue($_SESSION, [$keys]);
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

    /* ===========================================================================
     * END: HTTP request information
     * ===========================================================================
     */
}