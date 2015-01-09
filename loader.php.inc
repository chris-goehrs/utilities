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

require_once "App/Traits/TraitFlavor.php";
require_once "App/Traits/TraitLog.php";
require_once "App/Traits/TraitMySQL.php";
require_once "App/Traits/TraitRequest.php";
require_once "App/Traits/TraitSpecial.php";
require_once "App/Traits/TraitString.php";
require_once "App/Traits/TraitURL.php";
require_once "App/Traits/TraitValidation.php";

require_once "App/Utilities.php";

require_once "SpecialWindowsHack.php.inc";