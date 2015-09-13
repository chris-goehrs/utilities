<?php


class LoggingTest extends \Codeception\TestCase\Test
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
    public function testWriteToLog()
    {
		$log = \lillockey\Utilities\App\InstanceHolder::log();
		$log->write_to_log('');
    }
}