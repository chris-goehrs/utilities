<?php


class DBTest extends \Codeception\TestCase\Test
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
    public function testDB()
    {
		\lillockey\Utilities\App\InstanceHolder::set_config(new \lillockey\Utilities\Config\FullCustomConfig(
			'localhost', 'root', null, 'test'
		));
		$db = \lillockey\Utilities\App\InstanceHolder::db();

		//Records
		$records = $db->select_all('test');
		$this->assertTrue(sizeof($records) == 2, "Select all against 2 records");

    }
}