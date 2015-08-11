<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 8/11/2015
 * Time: 1:00 PM
 */

namespace lillockey\Utilities\App\Containers;


class ImageFileInformationResults extends FileInformationResults
{
	/**
	 * @param FileInformationResults $r
	 * @param                        $width
	 * @param                        $height
	 * @param                        $image_type
	 * @return ImageFileInformationResults
	 */
	public static function get_image_results_from_file_information(FileInformationResults &$r, $width, $height, $image_type)
	{
		return new ImageFileInformationResults($r->type(), $r->encoding(), $r->recommended_extension(), $r->size(), $width, $height, $image_type);
	}



	private $width;
	private $height;
	private $image_type;

	public function __construct($type, $encoding, $recommended_extension, $size, $width, $height, $image_type)
	{
		$this->width = intval($width);
		$this->height = intval($height);
		$this->image_type = intval($image_type);

		parent::__construct($type, $encoding, $recommended_extension, $size);
	}

	public function width()
	{
		return $this->width;
	}

	public function height()
	{
		return $this->height;
	}

	public function image_type()
	{
		return $this->image_type;
	}

	public function is_jpg()
	{
		return $this->image_type() == IMAGETYPE_JPEG || $this->image_type() == IMAGETYPE_JPEG2000;
	}

	public function is_gif()
	{
		return $this->image_type() == IMAGETYPE_GIF;
	}

	public function is_bmp()
	{
		return $this->image_type() == IMAGETYPE_BMP;
	}

	public function is_png()
	{
		return $this->image_type() == IMAGETYPE_PNG;
	}




}