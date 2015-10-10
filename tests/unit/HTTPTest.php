<?php

/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 7:14 PM
 */
class HTTPTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before()
	{
	}

	protected function _after()
	{
	}

	public function testGet()
	{
		//Get request -> Server expecting get
		$request = new \lillockey\Utilities\App\Http\GetRequest('http://httpbin.org/get');
		$response = $request->getResponse();
		$this->assertNotEmpty($response->getResponse(), "Get request to get test server - response should not be empty");
		$this->assertEquals(200,$response->getHTTPCode(), "Get request to get test server - code should be 200");

		//Get request -> Server expecting post
		$request = new \lillockey\Utilities\App\Http\GetRequest('http://httpbin.org/post');
		$response = $request->getResponse();
		$this->assertEquals(405, $response->getHTTPCode(), "Get request to post test server - code should be 405");
	}

	public function testPost()
	{
		$request = new \lillockey\Utilities\App\Http\PostRequest('http://httpbin.org/post', array());
		$response = $request->getResponse();
		$this->assertNotEmpty($response->getResponse(), "Post request to post test server - response should not be empty");
		$this->assertEquals(200, $response->getHTTPCode(), "Post request to post test server - code should be 200");

		$request = new \lillockey\Utilities\App\Http\PostRequest('http://httpbin.org/get', array());
		$response = $request->getResponse();
		$this->assertEquals(405, $response->getHTTPCode(), "Post request to get test server - code should be 405");
	}

}
