<?php


class FileInfoTest extends \Codeception\TestCase\Test
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
    public function testMe()
    {
        $utilities = \lillockey\Utilities\App\InstanceHolder::util();
		$this->assertTrue($utilities instanceof \lillockey\Utilities\App\Utilities, "Utilities loaded");

		$info = $utilities->get_file_information(__FILE__);
		$this->assertTrue($info instanceof \lillockey\Utilities\App\Containers\FileInformationResults, "Loading file type results");

		$this->assertEquals('text/x-php', $info->type(), 'Checking the MIME-type');
		$this->assertEquals(null, $info->recommended_extension(), 'Checking the recommended extension');
    }
}