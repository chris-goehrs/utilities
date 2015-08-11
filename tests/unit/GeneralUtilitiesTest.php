<?php


class GeneralUtilitiesTest extends \Codeception\TestCase\Test
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

    // tests
    public function testString()
    {
		$u = \lillockey\Utilities\App\InstanceHolder::util();
		$this->assertTrue($u instanceof \lillockey\Utilities\App\Utilities, "Utilities loaded");

		//variables to use
		$s = 'abcdefghijklmnopqrstuvwxyz';
		$ar = array('bob' => 'dole', 'foo' => 'bar', 'baz' => 1);
		$sr = serialize($ar);
		$smask = '12345678';

		//Left Is
		$this->assertTrue($u->str_left_is($s, 'abc'), "String left is abc - CI");
		$this->assertTrue($u->str_left_is($s, 'abc', false), "String left is abc - CS");
		$this->assertFalse($u->str_left_is($s, 'ABC', false), "String left is ABC - CS");

		//Right Is
		$this->assertTrue($u->str_right_is($s, 'xyz'), "String right is abc - CI");
		$this->assertTrue($u->str_right_is($s, 'xyz', false), "String right is abc - CS");
		$this->assertFalse($u->str_right_is($s, 'XYZ', false), "String right is ABC - CS");

		//String left
		$this->assertEquals('abc', $u->str_left($s, 3), "String left 3 characters");

		//String right
		$this->assertEquals('xyz', $u->str_right($s, 3), "String right 3 characters");

		//Random string
		$this->assertEquals(10, strlen($u->random_string(10)), "Random string - Valid Type, Lenth 10");
		$this->assertNull($u->random_string(10, 'bob'), "Random string - Invalid Type, Lenth 10");

		//Is string
		$this->assertTrue($u->is_str($s), "Is String - String Provided");
		$this->assertFalse($u->is_str($ar), "Is String - Array Provided");

		//Is Serialized
		$this->assertTrue($u->is_serialized($sr), "Is Serialized - Serialized Array Provided");
		$this->assertFalse($u->is_serialized($ar), "Is Serialized - Array Provided");
		$this->assertFalse($u->is_serialized($s), "Is Serialized - Plain String Provided");

		//Mask String
		$this->assertEquals("****5678", $u->mask_string($smask), "Mask string - all but last 4");
		$this->assertEquals("12345***", $u->mask_string($smask, '*', 5, LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST), "Mask string - all but first 5");
		$this->assertEquals("***45678", $u->mask_string($smask, '*', 3, LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__FIRST), "Mask string - first 3");
		$this->assertEquals("123456##", $u->mask_string($smask, '#', 2, LILLOCKEY_GENERAL_UTILITIES__MASK_TYPE__LAST), "Mask string - last 2 (#)");
		$this->assertEquals($smask, $u->mask_string($smask, '*', 1, 9001), "Mask string - invalid type");


	}

	public function testUrl()
	{
		//Set up the server array initially
		if(!isset($_SERVER)) $_SERVER = array();
		if(!is_array($_SERVER)) $_SERVER = array();
		$_SERVER['REQUEST_SCHEME'] = 'http';
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER['HTTP_REFERER'] = 'https://www.google.com';

		$u = \lillockey\Utilities\App\InstanceHolder::util();
		$this->assertTrue($u instanceof \lillockey\Utilities\App\Utilities, "Utilities loaded");

		//Base URL
		$this->assertEquals('http://example.com', $u->base_url(), "Base url - example.com");
	}

	public function testValidation()
	{
		$u = \lillockey\Utilities\App\InstanceHolder::util();
		$this->assertTrue($u instanceof \lillockey\Utilities\App\Utilities, "Utilities loaded");

		//Email validation
		$valid_email_mx_okay = "lillockey@gmail.com";
		$valid_email_mx_nokay = "bob@schlabadabee.com";
		$invalid_email = "bibbitybobbityboo";
		$this->assertFalse($u->validate_email($valid_email_mx_nokay, true), "Email Validation w/ MX - Good Form - Bad MX");
		$this->assertTrue($u->validate_email($valid_email_mx_nokay, false), "Email Validation w/o MX - Good Form - Bad MX");
		$this->assertTrue($u->validate_email($valid_email_mx_okay, true), "Email Validation w/ MX - Good Form - Good MX");
		$this->assertFalse($u->validate_email($invalid_email, false), "Email Validation w/o MX - Bad Form - Bad MX");


	}
}