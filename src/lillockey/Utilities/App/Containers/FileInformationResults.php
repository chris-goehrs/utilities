<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 8/10/2015
 * Time: 10:52 PM
 */

namespace lillockey\Utilities\App\Containers;

/**
 * Class FileInformationResults
 *
 * @package lillockey\Utilities\App\Containers
 */
class FileInformationResults
{
	private $type;
	private $encoding;
	private $recommended_extension;
	private $size;

	/**
	 * @param string $type - the MIME type
	 * @param string $encoding - the encoding for the file
	 * @param string $recommended_extension - if a common extension is recognized, provide the extension usually associated with it
	 * @param int    $size - the number of bytes in the data
	 */
	public function __construct($type, $encoding, $recommended_extension, $size)
	{
		$this->type = $type;
		$this->encoding = $encoding;
		$this->recommended_extension = $recommended_extension;
		$this->size = $size;
	}

	/**
	 * @return string
	 */
	public function type()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function encoding()
	{
		return $this->encoding;
	}

	/**
	 * @return string
	 */
	public function recommended_extension()
	{
		return $this->recommended_extension;
	}

	/**
	 * @return int
	 */
	public function size()
	{
		return $this->size;
	}
}