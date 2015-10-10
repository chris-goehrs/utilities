<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 7:48 PM
 */

namespace lillockey\Utilities\App\Http;


use lillockey\Utilities\App\XML\XmlElement;

class HttpResponse
{
	private $response;
	private $response_code;
	private $total_time;

	public function __construct($url, array &$curl_options)
	{
		//Better make sure this an array
		if(!is_array($curl_options)) $curl_options = array();

		//Set up curl
		$curl = curl_init($url);
		curl_setopt_array($curl, $curl_options);

		//Handle the response
		$this->response = curl_exec($curl);

		//Store some information
		$this->response_code = intval(curl_getinfo($curl, CURLINFO_HTTP_CODE));
		$this->total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);

		//Close curl
		curl_close($curl);
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function json($assoc = false)
	{
		return json_decode($this->getResponse(), $assoc);
	}

	/**
	 * Parses the response and returns the root XmlElement if valid
	 * @return XmlElement|null
	 */
	public function xml()
	{
		return XmlElement::parse($this->getResponse());
	}

	public function getHTTPCode()
	{
		return $this->response_code;
	}

	public function okay()
	{
		return $this->response_code == 200;
	}

	public function getTotalTime()
	{
		return $this->total_time;
	}

	public function __toString()
	{
		return $this->response;
	}


}