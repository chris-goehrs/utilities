<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 12/20/2014
 * Time: 8:11 PM
 */

require_once "Config/AbstractCustomConfig.php";
require_once "Config/DefaultCustomConfig.php";
require_once "Config/FullCustomConfig.php";
require_once "Config/WordpressCustomConfig.php";

require_once "Exceptions/DatabaseCredentialValidationException.php";
require_once "Exceptions/NotAnArrayException.php";

require_once "App/utilities.php";

require_once "SpecialWindowsHack.php.inc";