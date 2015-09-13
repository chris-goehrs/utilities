<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 10:56 AM
 */

namespace lillockey\Utilities\App\Log;

class File_Logger extends Abstract_Logger
{

	/**
	 * Grabs the location of the log file
	 * @param bool $absolute
	 * @return string
	 */
	public function get_log_location($absolute = true)
	{
		if($absolute)
			return realpath($this->config->log_location);
		else
			return $this->config->log_location;
	}

	/**
	 * Reads the last $number_of_lines from the log file
	 * @param int $number_of_lines
	 * @return array
	 */
	public function read_last_x_lines_of_log($number_of_lines = 10)
	{
		if(!$this->config->allow_write_to_log) return null;
		$fp = fopen($this->config->log_location, 'r');

		$idx   = 0;
		$lines = array();
		while(($line = fgets($fp)))
		{
			$lines[$idx] = $line;
			$idx = ($idx + 1) % $number_of_lines;
		}

		$p1 = array_slice($lines,    $idx);
		$p2 = array_slice($lines, 0, $idx);
		$ordered_lines = array_merge($p1, $p2);

		fclose($fp);

		return $ordered_lines;
	}

	/**
	 * Writes a line to the log file with a preceeding timestamp
	 * @param $line
	 * @return int|null
	 */
	public function write_to_log($line = '')
	{
		if(!$this->config->allow_write_to_log) return null;

		return file_put_contents(
			$this->config->log_location,					//File to write to
			date(DATE_ATOM)."\t$line\n",					//Line to write
			FILE_APPEND | LOCK_EX);							//Options
	}
}