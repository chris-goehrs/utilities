<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 1/20/2016
 * Time: 10:58 AM
 */

namespace lillockey\Utilities\App\Access\ArrayAccess;

use lillockey\Utilities\App\Access\Helpers\JsonErrorReportable;
use lillockey\Utilities\App\Access\ObjectAccess\JsonObject;

class JsonArray extends AccessibleArray implements JsonErrorReportable
{
    /**
     * JSON parsing error
     * @var int|null $json_error
     */
    private $json_error = null;

    /**
     * JSON parsing error message
     * @var bool|null|string $json_error_msg
     */
    private $json_error_msg = null;

    /**
     * JsonArray constructor.
     * @param string $json
     */
    public function __construct($json)
    {
        $j_array = json_decode($json, true);
        parent::__construct($j_array);
        $this->json_error = json_last_error();
        $this->json_error_msg = json_last_error_msg();
    }

    /**
     * JSON parsing error
     * @return int|null
     */
    public function getJsonError()
    {
        return $this->json_error;
    }

    /**
     * JSON parsing error message
     * @return bool|null|string
     */
    public function getJsonErrorMessage()
    {
        return $this->json_error_msg;
    }

    /**
     * Creates a json object from this object
     * @return JsonObject
     */
    public function toJsonObject()
    {
        $jo = new JsonObject('{}');
        foreach($this as $key => $value){
            $jo->set($key, $value);
        }
        return $jo;
    }
}