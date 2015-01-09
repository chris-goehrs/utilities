<?php

namespace Missilesilo\Utilities\App\Traits;

/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 9:54 PM
 */

trait TraitURL
{
    /* ===========================================================================
	 * START: URL helpers
	 * Inserted By: Christopher Goehrs 6/5/2014
	 * ===========================================================================
	 */

    /**
     * Get the base url for this request
     * @param string $append_to_base (anything set to immediately follow the base url)
     * @return string
     */
    public function base_url($append_to_base = '')
    {
        $str = $this->server('REQUEST_SCHEME').'://'.$this->server(['HTTP_HOST', 'SERVER_NAME']).(strlen($append_to_base)?'/'.rawurldecode($append_to_base):'');
        return $str;
    }

    /**
     * @param $base_url
     * @param int $status
     * @param array $subids
     * @return bool
     */
    public function redirect_to_url($base_url, $status = 302, array $subids = [])
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
    public function build_and_sanitize_url($base_url, array $subids = [])
    {
        return $this->sanitize_url($base_url.(sizeof($subids)?'?'.http_build_query($subids):''));
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
}