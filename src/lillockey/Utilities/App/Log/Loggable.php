<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 11:07 AM
 */

namespace lillockey\Utilities\App\Log;

interface Loggable
{
	public function get_log_location($absolute = true);
	public function read_last_x_lines_of_log($number_of_lines = 10);
	public function write_to_log($line = '');
}