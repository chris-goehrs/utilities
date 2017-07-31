<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/8/2015
 * Time: 9:45 PM
 */

namespace lillockey\Utilities\App\Access\ObjectAccess;


use lillockey\Utilities\App\Access\Helpers\JsonErrorReportable;
use lillockey\Utilities\App\InstanceHolder;

class JsonObject extends AccessibleObject implements JsonErrorReportable
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
     * JsonObject constructor.
     * @param string $json
     */
	public function __construct($json)
	{
        $std = json_decode($json);
        parent::__construct($std);
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
}