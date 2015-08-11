<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 8/11/2015
 * Time: 12:39 PM
 */

namespace lillockey\Utilities\App;

use lillockey\Utilities\App\Containers\ImageFileInformationResults;

class Image extends AbstractUtility
{
	//////////////////////////////////////////////////
	// Image Information
	//////////////////////////////////////////////////

	/**
	 * Retrieves image data for the string data provided
	 * @param string    $data - The raw image data
	 * @param bool      $validate - Should it validate before running?
	 * @return ImageFileInformationResults|string
	 * @throws \Exception
	 */
	public function get_image_information(&$data, $validate = true)
	{
		if($validate === true)
			if($reason = $this->validate_image_data($data) !== true)
				return $reason;

		$data_info = @InstanceHolder::util()->get_data_information($data);
		if(!$data_info) return "Couldn't get basic information on the data";

		try{
			list($srcw, $srch, $src_type, $src_attr, $mime) = array_values(getimagesizefromstring($data));	//Allow it to throw an exception
			return ImageFileInformationResults::get_image_results_from_file_information($data_info, $srcw, $srch, $src_type);
		}catch(\Exception $e){
			return "The data provided isn't an image";
		}

	}

	/**
	 * Retrieves image data for the image at the given path
	 * @param string    $path - The path to the iamge
	 * @param bool      $validate - Should it validate before running?
	 * @return ImageFileInformationResults|string
	 * @throws \Exception
	 */
	public function get_image_file_information($path, $validate = true)
	{
		if($validate === true)
			if($reason = $this->validate_image_file($path) !== true)
				return $reason;

		$data = file_get_contents($path);
		return $this->get_image_information($data, false);
	}

	//////////////////////////////////////////////////
	// Validation
	//////////////////////////////////////////////////

	/**
	 * Checks the given path for image validity
	 * @param $path - the path to be checked
	 * @return bool|string - true if okay, string (reason) if not okay
	 */
	public function validate_image_file($path)
	{
		if(!file_exists($path)) return "The file path doesn't exist";
		if(!is_readable($path)) return "The path exists but isn't readable";
		$data = file_get_contents($path);
		return $this->validate_image_data($data);
	}

	/**
	 * Checks the image data provided to see if it's actually an image
	 * @param $data
	 * @return bool|string
	 */
	public function validate_image_data(&$data)
	{
		$util = InstanceHolder::util();
		try{
			//This should only break if there's something hostile
			$util->get_data_information($data);
		}catch(\Exception $e){
			return "There was a problem getting the information";
		}

		//So ... it IS a real thing.
		try{
			//This will break if it isn't an image
			getimagesizefromstring($data);
		}catch(\Exception $e){
			return "The image wasn't a valid type after all";
		}

		//So ... it's an image ... seems legit
		return true;
	}

	//////////////////////////////////////////////////
	// Resize to Square
	//////////////////////////////////////////////////

	public function &gd_image_file_resize_to_square($path, $output_to_path = null)
	{
		if($reason = $this->validate_image_file($path) !== true) return $reason;
		return $this->gd_image_data_resize_to_square(file_get_contents($path), $output_to_path);
	}

	/**
	 * Creates a resized image that is square.  Cropping is automatically in the center of the image
	 * @param           $data - The image data to be considered
	 * @param null      $output_to_path[optional] - The path to which the resulting image should be written (setting this ensures that a boolean value will be returned)
	 * @param bool|true $run_validation[optional] - true runs the validation | else does not
	 * @return bool|string - False if there's an error.  Boolean if an output path is selected.  String if the output path is null.
	 */
	public function &gd_image_data_resize_to_square(&$data, $output_to_path = null, $run_validation = true)
	{
		if($run_validation === true)
			if($this->validate_image_data($data)) return false;

		$img = $this->get_image_information($data);
		if(!$img instanceof ImageFileInformationResults) return false;

		$output = false;

		//Calculate the image copy areas and sizes
		if($img->width() > $img->height()){
			//Width is bigger
			$src_x = intval(($img->width() - $img->height()) / 2);
			$src_y = 0;
			$src_w = $img->height();
			$src_h = $img->height();
		}elseif($img->width() < $img->height()){
			//Height is bigger
			$src_x = 0;
			$src_y = intval(($img->height() - $img->width()) / 2);
			$src_w = $img->width();
			$src_h = $img->width();
		}else{
			//It's apparently a square
			$src_x = 0;
			$src_y = 0;
			$src_w = $img->width();
			$src_h = $img->height();
		}
		$des_w = $src_w;
		$des_h = $src_h;

		$img_destination = imagecreatetruecolor($des_w, $des_h);
		$img_source = imagecreatefromstring($data);

		//Copy the calculated area into the small image
		imagecopyresized($img_destination, $img_source, 0, 0, $src_x, $src_y, $des_w, $des_h, $src_w, $src_h);

		if($output_to_path === null){
			ob_start();
		}

		if($img->image_type() == IMAGETYPE_JPEG || $img->image_type() == IMAGETYPE_JPEG2000) {
			$output = imagejpeg($img_destination, $output_to_path);
		}elseif($img->image_type() == IMAGETYPE_GIF) {
			$output = imagegif($img_destination, $output_to_path);
		}elseif($img->image_type() == IMAGETYPE_PNG){
			$output = imagepng($img_destination, $output_to_path);
		}elseif($img->image_type() == IMAGETYPE_BMP) {
			$output = imagewbmp($img_destination, $output_to_path);
		}

		if($output_to_path === null) {
			$output = ob_get_contents();
			ob_end_clean();
		}

		//Clean up, clean up, everybody, everywhere.
		imagedestroy($img_source);
		imagedestroy($img_destination);

		return $output;
	}

}