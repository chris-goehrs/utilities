<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 1/20/2016
 * Time: 11:00 AM
 */

namespace lillockey\Utilities\App\Access\Helpers;


interface JsonErrorReportable
{
    public function getJsonError();
    public function getJsonErrorMessage();


}