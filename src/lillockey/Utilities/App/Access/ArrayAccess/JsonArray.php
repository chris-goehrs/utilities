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
use lillockey\Utilities\App\InstanceHolder;

class JsonArray extends AccessibleArray implements JsonErrorReportable
{
    private $json_error = null;
    private $json_error_msg = null;

    public function __construct($json)
    {
        $util = InstanceHolder::util();
        if($util->is_json($json)) {
            $j_array = json_decode($json, true);
            parent::__construct($j_array);
            $this->json_error = json_last_error();
            $this->json_error_msg = json_last_error_msg();
        } else {
            parent::__construct(new \stdClass());
        }
    }

    public function getJsonError()
    {
        return $this->json_error;
    }

    public function getJsonErrorMessage()
    {
        return $this->json_error_msg;
    }

    public function toJsonObject()
    {
        $jo = new JsonObject('{}');
        foreach($this as $key => $value){
            $jo->set($key, $value);
        }
        return $jo;
    }
}