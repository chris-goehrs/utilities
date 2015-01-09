<?php

namespace Missilesilo\Utilities\App;

use Missilesilo\Utilities\App\Traits\TraitFlavor;
use Missilesilo\Utilities\App\Traits\TraitLog;
use Missilesilo\Utilities\App\Traits\TraitMySQL;
use Missilesilo\Utilities\App\Traits\TraitRequest;
use Missilesilo\Utilities\App\Traits\TraitSpecial;
use Missilesilo\Utilities\App\Traits\TraitString;
use Missilesilo\Utilities\App\Traits\TraitURL;
use Missilesilo\Utilities\App\Traits\TraitValidation;
use Missilesilo\Utilities\Config\AbstractCustomConfig;

define('MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH', false);
define('MISSILESILO_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET', true);



/**
 * Class Utilities
 * @package Missilesilo\Utilities\App
 */
class Utilities
{
    private $config;

	public function __construct(AbstractCustomConfig $config)
	{
		$this->config = $config;
	}

    //The class is split off to make things more readable
    use TraitMySQL;
	use TraitURL;
    use TraitFlavor;
    use TraitString;
    use TraitValidation;
	use TraitRequest;
    use TraitSpecial;
    use TraitLog;
}