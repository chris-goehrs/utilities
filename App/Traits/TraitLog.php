<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 10:33 PM
 */

namespace Missilesilo\Utilities\App\Traits;

trait TraitLog
{
    /* ==============================================================================================
	 * START: Log related
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Updated On: 8/12/2014
	 * Stuff related to writing to and reading from a log
	 * ---------------------------------------------------------------
	 * + 8/12/2014 - Added ability to retrieve the log file's path
	 * ==============================================================================================
	 */

    /**
     * Grabs the location of the log file
     * @param bool $absolute
     * @return string
     */
    public function get_log_file_location($absolute = true)
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
    public function write_to_log($line)
    {
        if(!$this->config->allow_write_to_log) return null;

        return file_put_contents(
            $this->config->log_location,					//File to write to
            date(DATE_ATOM)."\t$line\n",					//Line to write
            FILE_APPEND | LOCK_EX);							//Options
    }

    /**
     * Encode $to_encode to json, write json to log file, return json
     * @param $to_encode
     * @return string
     */
    function log_and_return_json($to_encode){
        $json_encoded = json_encode($to_encode);
        $this->write_to_log("JSON RETURNED: $json_encoded");
        return $json_encoded;
    }

    /**
     * Writes $_REQUEST nvp set to log with each key/value set being written as a new line
     */
    function write_request_fields_to_log()
    {
        //Get the maximum field length
        $max_length = 0;
        foreach($_REQUEST as $key=>$value) if(strlen($key) > $max_length) $max_length = strlen($key);

        //Add the fields to the log
        foreach($_REQUEST as $key=>$value){
            if(is_array($value))
                $valuep = "array(".sizeof($value).")";
            else
                $valuep = $value;

            $key_pad = str_pad($key, $max_length);
            $this->write_to_log("    $key_pad => $valuep");
        }
    }

    /* ===========================================================================
     * END: Log related
     * ===========================================================================
     */
}