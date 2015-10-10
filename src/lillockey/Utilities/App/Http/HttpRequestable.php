<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 6:18 PM
 */

namespace lillockey\Utilities\App\Http;


interface HttpRequestable
{
	/**
	 * Executes the request and returns the response
	 * @return HttpResponse - The response from the request
	 */
	public function getResponse();
}