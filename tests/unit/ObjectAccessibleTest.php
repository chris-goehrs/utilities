<?php


class ObjectAccessibleTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testAccessibleArray()
    {
        $a = new \lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray();
        $a['bob']['dole'] = true;
        $this->assertTrue(isset($a['bob']['dole']), "AccessibleArray - Multidimensional Array Not Set");
    }

    public function testJsonAccessibleArray()
    {
        $a = new \lillockey\Utilities\App\Access\ArrayAccess\JsonArray('{"Bob" : "Dole"}');
        $this->stringContains($a->string("Bob"), "Json Accessible Array");
    }

    public function testAccessibleObject()
    {
        $o = new stdClass();
        $o->a = array();
        $o->a['bob']['dole'] = true;
        $this->assertTrue(isset($o->a['bob']['dole']), "Attempting to use stdClass to handle a multi-dimensional array");

        $o = new \lillockey\Utilities\App\Access\ObjectAccess\AccessibleObject();
        $o->bob = array('dole' => true);
        //$o->bob['dole']['stuff'] = true;  //NOTE: Objects cannot do multidimensional arrays
        $this->assertTrue(isset($o->bob['dole']), "AccessibleObject - Array set");
    }

    public function testInstanceHolderGet()
    {
        global $_GET;
        if(!isset($_GET) || !is_array($_GET)) $_GET = array();
        $expected = \lillockey\Utilities\App\Access\ArrayAccess\GetArray::class;
        $instance = \lillockey\Utilities\App\InstanceHolder::get();
        $this->assertInstanceOf($expected, $instance, "Get class is not what was expected");
    }

    public function testInstanceHolderPost()
    {
        global $_POST;
        if(!isset($_POST) || !is_array($_POST)) $_POST = array();
        $expected = \lillockey\Utilities\App\Access\ArrayAccess\PostArray::class;
        $instance = \lillockey\Utilities\App\InstanceHolder::post();
        $this->assertInstanceOf($expected, $instance, "Post class is not what was expected");
    }

    public function testInstanceHolderRequest()
    {
        global $_REQUEST;
        if(!isset($_REQUEST) || !is_array($_REQUEST)) $_REQUEST = array();
        $expected = \lillockey\Utilities\App\Access\ArrayAccess\RequestArray::class;
        $instance = \lillockey\Utilities\App\InstanceHolder::request();
        $this->assertInstanceOf($expected, $instance, "Request class is not what was expected");
    }

    public function testInstanceHolderServer()
    {
        global $_SERVER;
        if(!isset($_SERVER) || !is_array($_SERVER)) $_SERVER = array();
        $expected = \lillockey\Utilities\App\Access\ArrayAccess\ServerArray::class;
        $instance = \lillockey\Utilities\App\InstanceHolder::server();
        $this->assertInstanceOf($expected, $instance, "Server class is not what was expected");
    }
}