<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 6:27 PM
 */

namespace lillockey\Utilities\App\Http;


abstract class AbstractRequest implements HttpRequestable
{
	protected $url = '';

	private $curl_options_to_set = array();
	
	public function __construct($base_url, array $query_arguments = null)
	{
		if($query_arguments == null) $query_arguments = array();
		$query = http_build_query($query_arguments);
		$base_url .= strpos($base_url, '?') ? '&' : '?';
		$this->url = $base_url . $query;

		$this->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
	}

	/**
	 * Executes the request and returns the response
	 *
	 * @return HttpResponse - The response from the request
	 */
	public function getResponse()
	{
		return new HttpResponse($this->url, $this->curl_options_to_set);
	}


	/**
	 * Stores a curl option for the time of execution
	 * @param int $curlopt - The cURL option to set
	 * @param     $value - The value of that option
	 * @return $this
	 */
	public function setCurlOption($curlopt, $value)
	{
		$this->curl_options_to_set[$curlopt]  = $value;
		return $this;
	}

	/**
	 * Unsets a cURL option
	 * @param int $curlopt - The cURL option
	 * @return $this
	 */
	public function unsetOption($curlopt)
	{
		unset($this->curl_options_to_set[$curlopt]);
		return $this;
	}

	public function setBasicAuthentication($username, $password)
	{
		return $this->setCurlOption(CURLOPT_USERPWD, "{$username}:{$password}");
	}

	public function setHttpHeader(array $headers)
	{
		$this->setCurlOption(CURLOPT_HTTPHEADER, $headers);
		$this->setCurlOption(CURLOPT_HEADER, sizeof($headers));
	}

	public function setTimeout($value = 30)
	{
		return $this->setCurlOption(CURLOPT_TIMEOUT, intval($value));
	}

	public function setReturnTransfer($value = true)
	{
		return $this->setCurlOption(CURLOPT_RETURNTRANSFER, $value);
	}

	public function setVerifyPeer($value = false)
	{
		return $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $value);
	}
}