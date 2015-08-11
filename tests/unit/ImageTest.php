<?php


class ImageTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
		\lillockey\Utilities\App\InstanceHolder::set_config(new \lillockey\Utilities\Config\DefaultCustomConfig());
    }

    protected function _after()
    {
    }

    // tests
    public function testImageInfo()
    {
		$im = \lillockey\Utilities\App\InstanceHolder::image();

		$image_large_jpg = __DIR__ . '/../files/1149112.jpg';
		$image_small_png = __DIR__ . '/../files/1149112.png';

		$large_jpg_info = $im->get_image_file_information($image_large_jpg);
		$this->assertTrue($large_jpg_info instanceof \lillockey\Utilities\App\Containers\ImageFileInformationResults, 'LARGE JPG - Checking if the results are the right class type');
		$this->assertEquals('image/jpeg', $large_jpg_info->type(), 'LARGE JPG - Checking MIME Type');
		$this->assertEquals(9054, $large_jpg_info->size(), 'LARGE JPG - Checking file size');
		$this->assertEquals(184, $large_jpg_info->width(), 'LARGE JPG - Checking width');
		$this->assertEquals(184, $large_jpg_info->height(), 'LARGE JPG - Checking height');
		$this->assertTrue($large_jpg_info->is_jpg(), 'LARGE JPG - Checking image type - is jpg');
		$this->assertFalse($large_jpg_info->is_gif(), 'LARGE JPG - Checking image type - is gif');
		$this->assertFalse($large_jpg_info->is_png(), 'LARGE JPG - Checking image type - is png');
		$this->assertFalse($large_jpg_info->is_bmp(), 'LARGE JPG - Checking image type - is bmp');


		$small_png_info = $im->get_image_file_information($image_small_png);
		$this->assertTrue($small_png_info instanceof \lillockey\Utilities\App\Containers\ImageFileInformationResults, 'SMALL PNG - Checking if the results are the right class type');
		$this->assertEquals('image/png', $small_png_info->type(), 'SMALL PNG - Checking MIME Type');
		$this->assertEquals(1422, $small_png_info->size(), 'SMALL PNG - Checking file size');
		$this->assertEquals(20, $small_png_info->width(), 'SMALL PNG - Checking width');
		$this->assertEquals(20, $small_png_info->height(), 'SMALL PNG - Checking height');
		$this->assertFalse($small_png_info->is_jpg(), 'SMALL PNG - Checking image type - is jpg');
		$this->assertFalse($small_png_info->is_gif(), 'SMALL PNG - Checking image type - is gif');
		$this->assertTrue($small_png_info->is_png(), 'SMALL PNG - Checking image type - is png');
		$this->assertFalse($small_png_info->is_bmp(), 'SMALL PNG - Checking image type - is bmp');
    }
}