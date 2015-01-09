<?php

namespace Missilesilo\Utilities\App\Traits;

/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 9:54 PM
 */

trait TraitFlavor
{
    /* ==============================================================================================
	 * START: Header/Flavor Helpers
	 * Added By: Christopher Goehrs
	 * Added On: 1/1/2015
	 * Header flavor related stuff.
	 * ==============================================================================================
	 */

    /**
     * Sets up the headers for the given content type
     * @param $mime - the mime-type for the data provided - e.g. application/json
     * @param $data
     * @param null $attachment_filename
     */
    public function flavor_echo($mime, $data, $attachment_filename = null)
    {
        header("Content-Type: $mime");
        header("Content-length: ".strlen($data));
        if($attachment_filename != null){
            header("Content-disposition: attachment; filename=\"$attachment_filename\"");
        }
        echo $data;
        die;
    }

    /**
     * Encodes $data and echos it with some pre-fabricated headers - convenience method
     * @param $data - raw data to be encoded using json_encode
     */
    public function flavor_echo_json($data)
    {
        $this->flavor_echo('application/json', json_encode($data));
    }
}