<?php


class ObjectAccessibleTest extends \Codeception\TestCase\Test
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
    public function testAccessibleArray()
    {
        $a = new \lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray();
        $a['bob']['dole'] = true;
        $this->assertTrue(isset($a['bob']['dole']), "AccessibleArray - Multidimensional Array Not Set");
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
}