<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 7:06 PM
 */

namespace lillockey\Utilities\App\Http;

class PostRequest extends AbstractRequest
{
	public function __construct($base_url, array $payload_fields, array $query_arguments = null)
	{
		parent::__construct($base_url, $query_arguments);

		$this->setCurlOption(CURLOPT_POST, sizeof($payload_fields) ? sizeof($payload_fields) : 1);
		$this->setCurlOption(CURLOPT_POSTFIELDS, $payload_fields);
	}
}