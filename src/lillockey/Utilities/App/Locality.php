<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 10:37 AM
 */

namespace lillockey\Utilities\App;


/**
 * Class Locality
 *
 * This class is dependent on not only database being connected but the country_list table existing in that database.
 * A copy of the table being relied on can be found in the Resources folder and is named "country_list.sql"
 *
 * @package lillockey\Utilities\App
 */
class Locality extends AbstractUtility
{
	private $db_instance;

	public function __construct(DB &$db, $name)
	{
		$this->db_instance = $db;
		parent::__construct($name);
	}

	/**
	 * Retrieves a list of countries
	 * @param array $exclude = a list of country codes to exclude from the results
	 * @return array
	 */
	public function get_countries(array $exclude = array())
	{
		$query = "SELECT COUNTRY_CODE, COUNTRY_NAME FROM country_list";
		$excount = 0;
		$variables = array();
		$where_and = ' WHERE';
		foreach($exclude as $ex){
			$excount++;             //Increment the count (starts at 1)
			$key = "ex_$excount";   //Create the key to be used
			$variables[$key] = $ex; //Add this to the variables

			//Add this to the query
			$query .= "{$where_and} `COUNTRY_CODE` NOT :$key";

			$where_and = ' AND';    //While we're at it, change "WHERE" to "AND" for the next variable
		}
		$query .= " ORDER BY COUNTRY_NAME ASC;";

		//Hammer these down to an associative array of countries and their respective codes where:
		//  key = Country code
		//  value = Country name
		$entries = $this->db_instance->run_raw_query_and_return_all_records($query, empty($variables) ? null : $variables);
		$countries = array();
		foreach($entries as $entry){
			$countries[$entry->COUNTRY_CODE] = $entry->COUNTRY_NAME;
		}
		return $countries;
	}

	/**
	 * @param string $code
	 * @return null|string
	 */
	public function get_country_name_from_code($code)
	{
		$item = $this->db_instance->select_one_by('country_list', 'COUNTRY_CODE', $code);
		if($item != null){
			return $item->COUNTRY_NAME;
		}
		return null;
	}

	/**
	 * @param string $name
	 * @return null|string
	 */
	public function get_country_code_from_name($name)
	{
		$item = $this->db_instance->select_one_by('country_list', 'COUNTRY_NAME', $name);
		if($item != null){
			return $item->COUNTRY_CODE;
		}
		return null;
	}

	/**
	 * @param string $code
	 * @return null|string
	 */
	public function get_postal_code_regex_from_code($code)
	{
		$item = $this->db_instance->select_one_by('country_list', 'COUNTRY_CODE', $code);
		if($item != null){
			return $item->POSTAL_CODE_REGEX;
		}
		return null;
	}

	/**
	 * Echos out complete list of countries.
	 * @param string     $selected
	 * @param bool       $include_blank
	 * @param array      $exclude
	 */
	public function echo_countries_options($selected = '', $include_blank = false, array $exclude = array())
	{
		$countries = $this->get_countries($exclude);
		if($include_blank)
			$this->echo_country_option('', '', $selected == '');
		foreach($countries as $country_code => $country_name){
			$this->echo_country_option($country_code, $country_name, $selected == $country_code || $selected == $country_name);
		}
	}

	/**
	 * Echos out the generated country option
	 * @param $country_code
	 * @param $country_name
	 * @param $selected
	 */
	private function echo_country_option($country_code, $country_name, $selected)
	{
		?>
		<option value="<?php echo htmlspecialchars($country_code);?>"<?php echo ($selected === true ? ' selected="selected"' : '')?>><?php echo htmlspecialchars($country_name);?></option>
		<?php
	}

	public function validate_address_minimal_set($street, $city, $state, $postal, $country = null)
	{
		return  $this->validate_address_street($street) &&
		$this->validate_address_city_state($city) &&
		$this->validate_address_city_state($state) &&
		$this->validate_address_postal($postal, $country);
	}

	public function validate_address_street($street)
	{
		if(strlen($street) < 2) return false;
		if(strlen($street) > 50) return false;
		return true;
	}

	public function validate_address_city_state($citystate)
	{
		if(strlen($citystate) < 2) return false;
		if(strlen($citystate) > 50) return false;
		return true;
	}

	public function validate_address_postal($postal, $country = null)
	{
		if(strlen($postal) < 1) return false;
		if(strlen($postal) > 50) return false;

		if(strlen($country) == 2){
			//Do a more specific check
			$country = $this->db_instance->select_one_by('country_list', 'COUNTRY_CODE', $country);
			if($country != null){
				//var_dump($country); die;
				if($country->POSTAL_CODE_REGEX != null){
					return (preg_match( $country->POSTAL_CODE_REGEX, $postal )?true:false);
				}
			}
		}

		return true;
	}
}