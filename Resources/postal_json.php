<?php

require_once __DIR__ . '/../loader.php.inc';

//Set up custom configuration
$mutil_config = new \Missilesilo\Utilities\Config\FullCustomConfig(
	$host = 'localhost',
	$user = 'root',
	$pass = null,
	$db = 	'cworld');

//Instantiate the utilities
$mutil = new \Missilesilo\Utilities\App\Utilities($mutil_config);

//Read postal.json
$json_text = file_get_contents('postal.json');

//Make it an associative array
$data = json_decode($json_text, true);

//Get the array
$postcode_array = $data['supplemental']['postalCodeData'];

foreach($postcode_array as $country_code => $regex){
	$regex = "/$regex/i";
	$mutil->update('country_list', 'COUNTRY_CODE', $country_code, ['POSTAL_CODE_REGEX' => $regex]);
}