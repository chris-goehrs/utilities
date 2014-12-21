<?php

namespace Missilesilo\Utilities\App;

use Missilesilo\Utilities\Config\AbstractCustomConfig;
use Missilesilo\Utilities\Exceptions\DatabaseCredentialValidationException;
use Missilesilo\Utilities\Exceptions\NotAnArrayException;

define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY', 'alpha');
define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC', 'alphanum');
define('MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL', 'alphanumspec');

define('MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH', false);
define('MISSILESILO_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET', true);

define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST', 1);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST', 2);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST', 3);
define('MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST', 4);

/**
 * Class Utilities
 * @package Missilesilo\Utilities\App
 */
class Utilities
{
    private $config;
	
	private $pdo_instance = null;
	
	public function __construct(AbstractCustomConfig $config)
	{
		$this->config = $config;
	}

    /**
     * Gets an instance of PDO based on the stored parameters
     * @param bool $create_new_instance - Forces the creation of a new instance of PDO
     * @return \PDO
     * @throws DatabaseCredentialValidationException
     */
	public function getPDO($create_new_instance = true)
	{	
		//Check for existing instance
		if($this->pdo_instance != null && $create_new_instance != true && $this->pdo_instance instanceof \PDO) return $this->pdo_instance;

		//Check for credential issues
		if(!$this->config->db_credentials_are_valid())
			throw new DatabaseCredentialValidationException('There was a problem with the db credentials');

		
		//Construct the options
		$host=		$this->config->host;
		$dbname=	$this->config->db;
		$username=	$this->config->user;
		$passwd=	$this->config->pass;
		$options=	[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
		
		//Construct and return the new instance
		return ($passwd == ''
				?$this->pdo_instance = new \PDO("mysql:host={$host}; dbname={$dbname}", $username, null, $options)
				:$this->pdo_instance = new \PDO("mysql:host={$host}; dbname={$dbname}", $username, $passwd, $options));
	}
	
	/**
	 * Runs a raw SQL query
	 * @param string $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @return array - all rows fetched by the query
	 */
	public function run_raw_query_and_return_all_records($query, array $arguments = null, $pdo_fetch_style = PDO::FETCH_OBJ)
	{
		$statement = $this->run_raw_query_and_return_statement($query, $arguments);
		return $statement->fetchAll($pdo_fetch_style);
	}

	/**
	 * Runs a raw SQL query
	 * @param string $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @return \PDOStatement - the statement after execution
	 */
	public function run_raw_query_and_return_statement($query, array $arguments = null)
	{
		$r = $this->execute_query($query, $arguments);
		return $r['statement'];
	}
	
	private function execute_query($query, array $arguments = null)
	{
		$exception = null;
		$db = $this->getPDO();
		$statement = $db->prepare($query);
		
		try{
			$execute_results = $statement->execute($arguments);
		}catch(\Exception $e){
			$execute_results = false;
			$exception = $e;
		}
		
		$results = [
		'query' => $query,
		'arguments' => $arguments,
		'db' => $db,
		'exec' => $execute_results,
		'statement' => $statement,
		'exception' => $exception,
		'id' => $db->lastInsertId(),
		];
		
		return $this->log_query($results);
	}
	
	private function log_query(array &$results)
	{
		if(!$this->config->db_log_queries) return $results;
		
		$this->write_to_log('QUERY_EXECUTED!');
		$this->write_to_log('    Query: '. $results['query']);
		if(is_array($results['arguments'])){
			foreach($results['arguments'] as $key => $value){
				$this->write_to_log("    Query Argument '$key' = '$value'");
			}
		}
		$this->write_to_log('  Execution Result: '.($results['exec']?'ok':'NOT OK!'));
		$this->write_to_log('  Insert ID Value: '.$results['id']);
		$statement = $results['statement'];
		if(!$results['exec']){
			$err = $statement->errorInfo();
			$this->write_to_log('  !!Error Code: '. $err[0]);
			$this->write_to_log('  !!Driver Error Code: '. $err[1]);
			$this->write_to_log('  !!Driver Error Message: '. $err[2]);
		}
		if($results['exception'] instanceof \Exception){
			$ex = $results['exception'];
			$this->write_to_log('  Exception: '.$ex->getMessage().'\n'.$ex->getTraceAsString());
		}
		
		return $results;
	}
	
	/**
	 * Runs a raw SQL query
	 * @param string $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @return boolean - the results of the ->execute statement
	 */
	public function run_raw_query_and_return_query_status($query, array $arguments = null)
	{
		$r = $this->execute_query($query, $arguments);
		return $r['exec'];
	}
	
	private function build_where(array $where, $where_prefix = ' ', $variable_prefix = '_where_')
	{
		foreach($where as $wherefield=>$wherevalue){
			if($this->field_name_is_valid($wherefield) === false) return null;
		}
		
		$return_me = [
			'query' => '',
			'array' => []
		];
		
		//Build the query based on the where array
		if(sizeof($where) == 0){
			return $return_me;
		}else{
			$query = "{$where_prefix}WHERE ";
			$first = true;
				
			foreach($where as $wherefield=>$wherevalue){
				if($first){
					$first = false;
				}else{
					$query .= " AND ";
				}
				$key = $variable_prefix.$wherefield;
				$query .= "`$wherefield` = :$key";
				$return_me['array'][$key] = $wherevalue;
			}
			
			$return_me['query'] = $query;
			return $return_me;
		}
	}
	
	private function build_order($orderby, $asc_desc)
	{
		if($orderby !== null && $this->field_name_is_valid($orderby) === false) return null;
		if($orderby !== null && $asc_desc != 'ASC' && $asc_desc != 'DESC') return null;
		
		return ($orderby === null?'':" ORDER BY `$orderby` $asc_desc");
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string $field
	 * @param array $where
	 * @param string $orderby
	 * @param string $asc_desc
	 * @return NULL|mixed:
	 */
	public function select_all_distinct_by($table, $field, array $where = [], $orderby = null, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		//Clean the questionable fields
		if($this->field_name_is_valid($field) === false) return null;
		if($this->field_name_is_valid($table) === false) return null;
		$ordertext = $this->build_order($orderby, $asc_desc);
		$wherear = $this->build_where($where);
		if($ordertext === null || $wherear === null) return null;
		
		$query = "SELECT{$this->config->db_cache_text} DISTINCT `$field` FROM `$table`{$wherear['query']}$ordertext";
		
		$statement = $this->run_raw_query_and_return_statement($query, sizeof($wherear['array'])?$wherear['array']:null);
		return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
	}
	
	/**
	 * Retrieves the first row in the table with $field matching $value
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $orderby [optional] if null, no ordering will be done
	 * @param string $asc_desc [optional] must be either ASC or DESC
	 * @return \stdObject|NULL
	 */
	public function select_one_by($table, $field, $value, $orderby = null, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		//Clean the questionable fields
		if($this->field_name_is_valid($field) === false) return null;
		if($this->field_name_is_valid($table) === false) return null;
		if($orderby !== null && $this->field_name_is_valid($orderby) === false) return null;
		if($orderby !== null && $asc_desc != 'ASC' && $asc_desc != 'DESC') return null;
		
		//Build the ordering text (if required)
		$ordertext = ($orderby === null?'':" ORDER BY `$orderby` $asc_desc");
		
		$query = "SELECT{$this->config->db_cache_text} * FROM `$table` WHERE `$field` = :id LIMIT 1";
		$statement = $this->run_raw_query_and_return_statement($query, ['id' => $value]);
		return $statement->fetch(\PDO::FETCH_OBJ);
	}
	
	/**
	 * Retrieves the column from the first row found
	 * @param string $table
	 * @param string $field
	 * @param array $where
	 * @param string $orderby
	 * @param string $asc_desc
	 * @return NULL|mixed
	 */
	public function select_one_value_by($table, $field, array $where = [], $orderby = null, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		if($this->field_name_is_valid($field) === false) return null;
		if($this->field_name_is_valid($table) === false) return null;
		$ordertext = $this->build_order($orderby, $asc_desc);
		$wherear = $this->build_where($where);
		if($ordertext === null || $wherear === null) return null;
		
		$query = "SELECT{$this->config->db_cache_text} `$field` FROM `$table`{$wherear['query']}$ordertext LIMIT 1";
		
		$statement = $this->run_raw_query_and_return_statement($query, sizeof($wherear['array'])?$wherear['array']:null);
		return $statement->fetch(\PDO::FETCH_COLUMN, 0);
	}
	
	/**
	 * Retrieves all rows in the table with $field matching $value
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $orderby [optional] if null, no ordering will be done
	 * @param string $asc_desc [optional] must be either ASC or DESC
	 * @return NULL|array (stdObject)
	 */
	public function select_all_by($table, $field, $value, $orderby = null, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		//Clean the questionable fields
		if($this->field_name_is_valid($field) === false) return null;
		if($this->field_name_is_valid($table) === false) return null;
		if($orderby !== null && $this->field_name_is_valid($orderby) === false) return null;
		if($orderby !== null && $asc_desc != 'ASC' && $asc_desc != 'DESC') return null;
		
		//Build the ordering text (if required)
		$ordertext = ($orderby === null?'':" ORDER BY `$orderby` $asc_desc");
		
		$query = "SELECT{$this->config->db_cache_text} * FROM `$table` WHERE `$field` = :id$ordertext";
		return $this->run_raw_query_and_return_all_records($query, ['id' => $value], \PDO::FETCH_OBJ);
	}
	
	/**
	 * Retrieves all rows in the table ordered
	 * @param string $table - table to dump
	 * @param string $orderby - field to order by
	 * @param string $asc_desc - either 'ASC' or 'DESC'
	 * @return array|NULL
	 */
	public function select_all_order($table, $orderby, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return null;
		if($this->field_name_is_valid($orderby) === false) return null;
		if($asc_desc !== 'ASC' && $asc_desc !== 'DESC') return null;
		
		
		$query = "SELECT{$this->config->db_cache_text} * FROM `$table` ORDER BY $orderby $asc_desc";
		return $this->run_raw_query_and_return_all_records($query, null, \PDO::FETCH_OBJ);
	}
	
	/**
	 * Counts the number of rows by using the following query
	 * SELECT COUNT(*) FROM $table WHERE $field = $value
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @return number (-1 if there is an error, >= 0 if it queried correctly)
	 */
	public function count_by($table, $field, $value)
	{
        $table = $this->config->table($table);

		if($this->field_name_is_valid($field) === false) return -1;
		if($this->field_name_is_valid($table) === false) return -1;
		
		$query = "SELECT{$this->config->db_cache_text} COUNT(*) FROM `$table` WHERE `$field` = :id";
		$statement = $this->run_raw_query_and_return_statement($query, ['id' => $value]);
		return intval($statement->fetchColumn(0));
	}
	
	public function count_by_multi($table, array $where = [], $orderby = null, $asc_desc = 'ASC')
	{
        $table = $this->config->table($table);

		//Clean the questionable fields
		if($this->field_name_is_valid($table) === false) return null;
		$ordertext = $this->build_order($orderby, $asc_desc);
		$wherear = $this->build_where($where);
		if($ordertext === null || $wherear === null) return null;
		
		$query = "SELECT{$this->config->db_cache_text} COUNT(*) FROM `$table`{$wherear['query']}$ordertext";
		$statement = $this->run_raw_query_and_return_statement($query, sizeof($wherear['array'])?$wherear['array']:null);
		return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
	}
	
	/**
	 * Inserts a new record into the given table with columns/values determined by associative array $fields
	 * @param string $table
	 * @param array $fields
	 * @return boolean
	 */
	public function insert($table, array $fields)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		
		//Build column/value lists
		$columns = "";
		$values = "";
		$i = 0;
		$submitted_values = [];
		foreach($fields as $column=>$value)
		{
			if($this->field_name_is_valid($column) === false) {continue;}
			if(empty($value)) continue;
			
			if($this->str_left_is($value, '#__')){
				$function_to_use = substr($value, 3, strlen($value) - 3);
				
				if($i == 0)
				{	//If this is the first column/value pair
					$columns = $column;				//Make the columns value = the column name
					$values = $function_to_use;		//Set the value to the premade key
				}
				else
				{	//If this is any subsequent column/value pair, precede both the value and the column with a ','
					$columns .= ", $column";		//Add the column value to the columns list
					$values .= ", $function_to_use";//Add the premade key to the values list
				}
			}else{
			
				$key = "v$i";						//Make a reusable key
				$submitted_values[$key] = $value;	//Add the validated column's value into the array to be submitted to the query
				
				if($i == 0)
				{	//If this is the first column/value pair
					$columns = $column;			//Make the columns value = the column name
					$values = ":$key";			//Set the value to the premade key
				}
				else
				{	//If this is any subsequent column/value pair, precede both the value and the column with a ','
					$columns .= ", $column";	//Add the column value to the columns list
					$values .= ", :$key";		//Add the premade key to the values list
				}
			}
			
			$i++;	//Next
		}
		
		//Quick accepted column check.
		if(sizeof($submitted_values) < 1) return false;
		
		//Create the query
		$query = "INSERT INTO `$table` ($columns) VALUES ($values);";
		
		//Run the query
		$r = $this->execute_query($query, $submitted_values);
		return ($r['exec']?$r['id']:false);
	}
	
	public function update_by_multi($table, array $where, array $fields)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		if(sizeof($fields) < 1) return false;
		
		//Prepare the basic variables
		$query = "UPDATE `$table` SET";		//The basic query string
		$i = 0;								//Counter for number of fields
		$submitted_fields = [];				//The fields that have been kept
		
		foreach($fields as $column_name=>$column_value){
			if($this->field_name_is_valid($column_name) === false) continue;	//Skip it if its a bad column name
				
			$prefab_key = "v$i";							//Make the prefabricated key
			$submitted_fields[$prefab_key] = $column_value;	//Add the field value to the array to be submitted
				
			//Add the column to the list to be updated
			if($i == 0){
				$query .= " `$column_name`=:$prefab_key";
			}else{
				$query .= ", $column_name`=:$prefab_key";
			}
				
			//Next
			$i++;
		}
		
		//Quick accepted column check.
		if(sizeof($submitted_fields) < 1) return false;
		
		//Build WHERE clause
		$wherear = $this->build_where($where);
		if($wherear === null) return false;
		
		//Merge arrays together
		$final_ar = array_merge($submitted_fields, $wherear['array']);
		if(!is_array($final_ar)) return false;		
		
		//Add where to the query
		$query .= $wherear['query'];
		
		//Execute query
		return $this->run_raw_query_and_return_query_status($query, $final_ar);
	}
	
	/**
	 * Updates a record set in the given table where the column ($id_field) = the given value ($id_value).  The associative
	 * array ($fields) is used to update the columns
	 * @param string $table
	 * @param string $id_field
	 * @param string $id_value
	 * @param array $fields
	 * @return boolean
	 */
	public function update($table, $id_field, $id_value, array $fields)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($id_field) === false) return false;
		if($this->field_name_is_valid($table) === false) return false;
		if(sizeof($fields) < 1) return false;
		
		//Prepare the basic variables
		$query = "UPDATE `$table` SET";		//The basic query string
		$i = 0;								//Counter for number of fields
		$submitted_fields = [];				//The fields that have been kept
		
		foreach($fields as $column_name=>$column_value){
			if($this->field_name_is_valid($column_name) === false) continue;	//Skip it if its a bad column name
			
			$prefab_key = "v$i";							//Make the prefabricated key
			$submitted_fields[$prefab_key] = $column_value;	//Add the field value to the array to be submitted
			
			//Add the column to the list to be updated
			if($i == 0){
				$query .= " `$column_name`=:$prefab_key";
			}else{
				$query .= ", `$column_name`=:$prefab_key";
			}
			
			//Next
			$i++;
		}
		
		//Quick accepted column check.
		if(sizeof($submitted_fields) < 1) return false;
		
		//Build WHERE clause
		$id_val_key = 'id____value';
		$query .= " WHERE `$id_field`=:$id_val_key";
		$submitted_fields[$id_val_key] = $id_value;
		
		//Execute query
		return $this->run_raw_query_and_return_query_status($query, $submitted_fields);
	}
	
	/**
	 * Deletes the records where the field ($id_field) equals the value ($id_value)
	 * @param string $table
	 * @param string $id_field
	 * @param string $id_value
	 * @return boolean
	 */
	public function delete($table, $id_field, $id_value)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		if($this->field_name_is_valid($id_field) === false) return false;
		
		$query = "DELETE FROM `$table` WHERE `$id_field`=:id_value";
		return $this->run_raw_query_and_return_query_status($query, ['id_value' => $id_value]);
	}
	
	/**
	 * Deletes the records where fields ($id_field1, $id_field2) equal the values ($id_value1, $id_value2)
	 * @param string $table
	 * @param string $id_field1
	 * @param string $id_value1
	 * @param string $id_field2
	 * @param string $id_value2
	 * @return boolean
	 */
	public function delete2($table, $id_field1, $id_value1, $id_field2, $id_value2)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		if($this->field_name_is_valid($id_field1) === false) return false;
		if($this->field_name_is_valid($id_field2) === false) return false;
		
		$query = "DELETE FROM `$table` WHERE `$id_field1`=:id_value1 AND `$id_field2`=:id_value2";
		return $this->run_raw_query_and_return_query_status($query, ['id_value1' => $id_value1, 'id_value2' => $id_value2]);
	}
	
	/**
	 * Drops the named table
	 * @param string $table
	 * @return boolean
	 */
	public function drop_table($table)
	{
        $table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		$query = "DROP TABLE IF EXISTS `$table`";
		return $this->run_raw_query_and_return_query_status($query);
	}
	
	/* ===========================================================================
	 * START: URL helpers
	 * Inserted By: Christopher Goehrs 6/5/2014
	 * ===========================================================================
	 */

    /**
     * @param $base_url
     * @param int $status
     * @param array $subids
     * @return bool
     */
	public function redirect_to_url($base_url, $status = 302, array $subids = [])
	{
		$sanitized_url = $this->build_and_sanitize_url($base_url, $subids);
		$header = "Location: $sanitized_url";
		header($header, true, $status);
		return true;
	}

    /**
     * @param $url
     * @param int $status
     * @return bool
     */
	public function redirect_to_url_raw($url, $status = 302)
	{
		$header = "Location: $url";
		header($header, true, $status);
		return true;
	}

    /**
     * @param int $status
     * @return bool
     */
	public function redirect_to_base_url($status = 302)
	{
		return $this->redirect_to_url_raw($this->base_url(), $status);
	}

    /**
     * Constructs a sanitized url from a base url and an associative array
     * @param $base_url
     * @param array $subids
     * @return string
     */
	public function build_and_sanitize_url($base_url, array $subids = [])
	{
		return $this->sanitize_url($base_url.(sizeof($subids)?'?'.http_build_query($subids):''));
	}

    /**
     * Sanitizes the url
     * @param $url
     * @return string
     */
	public function sanitize_url($url)
	{
		$url = (string) $url;
		$url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $url);
		$url = preg_replace('/\0+/', '', $url);
		$url = preg_replace('/(\\\\0)+/', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');
		
		$count = 1;
		while ( $count ) {
			$url = str_replace( $strip, '', $url, $count );
		}
		
		$url = $this->_deep_replace($strip, $url);
        return $url;
	} 
	
	/**
	 * Convenience method, really.  Checks if any headers have already been sent ... just like its namesake.
	 * @return boolean
	 */
	public function headers_sent()
	{
		return headers_sent();
	}
	
	/* ===========================================================================
	 * START: String helpers
	 * Inserted By: Christopher Goehrs 5/31/2014
	 * ===========================================================================
	 */

    private function _deep_replace($search, $subject)
    {
        $subject = (string) $subject;

        $count = 1;
        while ( $count ) {
            $subject = str_replace( $search, '', $subject, $count );
        }

        return $subject;
    }

	/**
	 * Checks to see if a string starts with another string
	 * @param string $str_subject - the string to check
	 * @param string $str_is - does $str_subject start with this?
	 * @param boolean $ignore_case
	 * @return boolean - true if $str_subject starts with $str_is, false if otherwise
	 */
	public function str_left_is($str_subject, $str_is, $ignore_case = false)
	{
		//Basic assumptions
		if($str_subject === $str_is) return true;
		if($str_subject === null && $str_subject === null) return true;
		if($str_subject === null) return false;
		if(strlen($str_subject) === strlen($str_is) && strlen($str_subject) == 0) return true;
		if(strlen($str_subject) < strlen($str_is)) return false;
	
		//Check for ignored case request
		if($ignore_case === true){
			$str_subject = strtolower($str_subject);
			$str_is = strtolower($str_is);
		}
	
		return substr($str_subject, 0, strlen($str_is)) === $str_is;
	}
	
	/**
	 * Checks to see if a string starts with another string
	 * @param string $str_subject - the string to check
	 * @param string $str_is - does $str_subject start with this?
	 * @param boolean $ignore_case
	 * @return boolean - true if $str_subject starts with $str_is, false if otherwise
	 */
	public function str_right_is($str_subject, $str_is, $ignore_case = false)
	{
		//Basic assumptions
		if($str_subject === $str_is) return true;
		if($str_subject === null && $str_subject === null) return true;
		if($str_subject === null) return false;
		if(strlen($str_subject) === strlen($str_is) && strlen($str_subject) == 0) return true;
		if(strlen($str_subject) < strlen($str_is)) return false;
	
		//Check for ignored case request
		if($ignore_case === true){
			$str_subject = strtolower($str_subject);
			$str_is = strtolower($str_is);
		}
		
		$start_location = strlen($str_subject) - strlen($str_is);
		$search_length = strlen($str_is);
		$str_subject_contents = substr($str_subject, $start_location, $search_length);
		
		return $str_subject_contents == $str_is;
	}
	
	/**
	 * Generates a randomized string
	 * @author Christopher R. Goehrs
	 * @since 5/31/2014
	 * @param integer $length
	 * @param string $type - can be any of the following values<ul>
	 * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY</li>
	 * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC</li>
	 * <li>MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL</li></ul>
	 * @return NULL|string null if invalid type or length isn't a valid integer / otherwise random string
	 */
	public function random_string($length, $type=MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL){
		$cur = 0;
		$str = '';
		
		if(!is_int($length)) return null;
		
		//Set up the character set
		$chars = null;
		if($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_ONLY)
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		elseif($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC)
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		elseif($type == MISSILESILO_GENERAL_UTILITIES__RANDOM_STRING__TYPE__ALPHA_NUMERIC_SPECIAL)
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-{}[]?/>.<,`~|\\';
		if($chars == null) return null;
		
		//Calculate the random character floor and ceiling values
		$chars_floor = 0;
		$chars_ceiling = strlen($chars) - 1;
		
		//Seed the randomizer
		srand(time());
		while($cur < $length){
			//Select and add the random character to the string
			$str .= $chars[rand($chars_floor, $chars_ceiling)];
			$cur++;
		}
		
		return $str;
	}

    /**
     * Evaluates if the value provided is a string or can be evaluated as a string
     * @param $value
     * @return bool true if it can / false if it cannot
     */
	public function is_str($value)
	{
		return !is_array($value) && 
		(
				(!is_object( $value ) && settype( $value, 'string' ) !== false) || 
				(is_object($value) && method_exists($value, '__toString'))
		); 
	}
	
	/**
	 * Checks if a string is currently serialized
	 * @param string $str
	 * @return boolean true if serialized | false otherwise
	 */
	public function isSerialized($str) {
		return ($str == serialize(false) || @unserialize($str) !== false);
	}
	
	/**
	 * Strips slashes by reference in the given string
	 * @param $value
	 */
	private function strip_slashes(&$value)
	{
		if($this->is_str($value)) 
			$value = stripslashes($value);
		//else
			//do nothing (implied)
	}
	
	/**
	 * It, um, masks a string?  The default values are set up for masking a credit card number
	 * @param string $subject - the string to be masked
	 * @param string $mask - a single character used to mask the string
	 * @param int $mask_length_value - this is the length as it relates to the mask type
	 * 		for "all_but" mask types, this represents what remains visible
	 * 		fir "first"/"last" mask types, this represents the amount that is being covered up
	 * @param int $mask_type - must be one of the following:
	 * 	<ul><li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST</li>
	 * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST</li>
	 * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST</li>
	 * 		<li>MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST</li></ul>
	 * @return string - The masked string
	 */
	public function mask_string($subject, $mask = '*', $mask_length_value = 4, $mask_type = MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST)
	{
		if(!is_int($mask_length_value)) return $subject;
		if(!$this->is_str($subject)) return $subject;
		if(!$this->is_str($mask)) return $subject;
		if(!is_int($mask_type)) return $subject;
		if(strlen($mask) > 1) $mask  = substr($mask, 0, 1);
		
		$slen = strlen($subject);
		
		switch($mask_type){
			case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__FIRST:
				$remaining_visible = $slen - $mask_length_value;
				if($remaining_visible < 1) return str_pad('', $slen, $mask);
					
				return str_pad('', $mask_length_value, $mask).$this->str_right($subject, $remaining_visible);
				break;
			case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__LAST:
				$remaining_visible = $slen - $mask_length_value;
				if($remaining_visible < 1) return str_pad('', $slen, $mask);
					
				return $this->str_left($subject, $remaining_visible).str_pad('', $mask_length_value, $mask);
				break;
			case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_FIRST:
				$mask_length = $slen - $mask_length_value;
				if($mask_length < 1) return $subject;
				
				return $this->str_left($subject, $mask_length_value).str_pad('', $mask_length, $mask);
				break;
			case MISSILESILO_GENERAL_UTILITIES__MASK_TYPE__ALL_BUT_LAST:
				$mask_length = $slen - $mask_length_value;
				if($mask_length < 1) return $subject;
				
				return str_pad('', $mask_length, $mask) . $this->str_right($subject, $mask_length_value);
				break;
		}
		
		return $subject;
	}
	
	public function str_right($subject, $length)
	{
		if(!$this->is_str($subject)) return null;
		if(!is_int($length)) return null;
		if($length > strlen($subject)) return $subject;
		
		return substr($subject, strlen($subject) - $length, $length);
	}
	
	public function str_left($subject, $length)
	{
		if(!$this->is_str($subject)) return null;
		if(!is_int($length)) return null;
		if($length > strlen($subject)) return $subject;
		
		return substr($subject, 0, $length);
	}
	
	/* ===========================================================================
	 * END: String helpers
	 * ===========================================================================
	 */
	
	/* ==============================================================================================
	 * START: Validation helpers
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * This validates various field types
	 * ==============================================================================================
	 */
	
	/**
	 * Filters the column/table name to match MYSQL documentation
	 *
	 * Based on: http://stackoverflow.com/questions/4977898/check-for-valid-sql-column-name
	 * @param string $field
	 * @return mixed
	 */
	public function field_name_is_valid($field)
	{
		return filter_var($field, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '^[a-zA-Z_][a-zA-Z0-9_]*$^']]);
	}
	
	/**
	 * Checks to see if the provided email address is valid
	 * @param string $email - the email address to be validated
	 * @param boolean $check_mx - (optional) true will attempt to validate the DNS records for the given email address
	 * @return boolean - true if valid/false if invalid
	 */
	public function validate_email($email, $check_mx = true)
	{
		$sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$email_as_address_is_okay = $sanitized_email?true:false;
		if(!$check_mx || !$email_as_address_is_okay) return $email_as_address_is_okay;
		
		list($user, $domain) = explode('@', $sanitized_email);
		return checkdnsrr($domain, 'MX');
	}
	
	/**
	 * Validates the credit card number using the Luhn algorithm and a null checker
	 * 
	 * Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
 	 * This code has been released into the public domain, however please      *
 	 * give credit to the original author where possible.                      *
	 * 
	 * @param string $number - The credit card number to be validated
	 * @return boolean true if okay/false if not
	 */
	public function validate_credit_card_number($number)
	{
		if(strlen($number) == 0) return false;
		
		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number=preg_replace('/\D/', '', $number);
		
		// Set the string length and parity
		$number_length=strlen($number);
		$parity=$number_length % 2;
		
		// Loop through each digit and do the maths
		$total=0;
		for ($i=0; $i<$number_length; $i++) {
			$digit=$number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit*=2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit-=9;
				}
			}
			// Total up the digits
			$total+=$digit;
		}
		
		// If the total mod 10 equals 0, the number is valid
		$is_valid = ($total % 10 == 0) ? TRUE : FALSE;
		return $is_valid;
	}
	
	/**
	 * Validates a credit card expiration month and year. <br/>
	 * The expiration is considered valid when it is both in the future and within 10 years of the current date
	 * 
	 * @param string $month - MM
	 * @param string $year - YYYY
	 * @return boolean true if valid/false if not 
	 */
	public function validate_credit_card_expiration($month, $year)
	{
		//Grab the expiration time
		$exp_ts = mktime(0, 0, 0, ($month + 1), 1, $year);
		
		//Grab the current time
		$cur_ts = time();
		
		// Don't validate for dates more than 10 years in future.
		$max_ts = $cur_ts + (10 * 365 * 24 * 60 * 60);
		
		//Validate the date
		return $exp_ts > $cur_ts && $exp_ts < $max_ts;
	}
	
	/**
	 * Validates a phone number
	 * 
	 * @param string $number
	 * @return boolean true if valid/false if not
	 */
	public function validate_phone_number($number)
	{
		$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
		return (preg_match( $regex, $number )?true:false);
	}
	
	/* ===========================================================================
	 * END: Validation helpers
	 * ===========================================================================
	 */
	
	/* ==============================================================================================
	 * START: HTTP request information
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Checks various server and request information
	 * ==============================================================================================
	 */
	
	/**
	 * Checks to see if the current request is a post
	 * @return boolean
	 */
	public function request_is_post()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/**
	 * Retrieves the refering domain from $_SERVER['HTTP_REFERER']
	 * @return string
	 */
	public function refering_domain()
	{
		return parse_url($this->referer(), PHP_URL_HOST);
	}
	
	/**
	 * Convenience method for 
	 * @return string
	 */
	public function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
	/**
	 * 
	 * @param string $append_to_base (anything set to immediately follow the base url)
	 * @return string
	 */
	public function base_url($append_to_base = '')
	{
		$str = $this->server('REQUEST_SCHEME').'://'.$this->server(['HTTP_HOST', 'SERVER_NAME']).(strlen($append_to_base)?'/'.rawurldecode($append_to_base):'');
		return $str;
	}
	
	/**
	 * Searches the $_SERVER variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function server($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_SERVER)){ 
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SERVER is not an array');
			return null;
		} 
		
		if($this->is_str($keys))
			return $this->getArrayValue($_SERVER, [$keys]);
		else{
			if(is_array($keys))
				return $this->getArrayValue($_SERVER, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}
	
	/**
	 * Searches the $_GET variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function get($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_GET)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_GET is not an array');
			return null;
		}
		
		if($this->is_str($keys))
			return $this->getArrayValue($_GET, [$keys]);
		else{
			if(is_array($keys))
				return $this->getArrayValue($_GET, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}
	
	/**
	 * Searches the $_POST variable for the keys
	 * @param array/string/object $keys
	 * @param boolean $throw_exception_when_cant_be_searched
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function post($keys, $throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if(!is_array($_POST)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_POST is not an array');
			return null;
		}
		
		if($this->is_str($keys))
			return $this->getArrayValue($_POST, [$keys]);
		else{
			if(is_array($keys))
				return $this->getArrayValue($_POST, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}
	
	
	/**
	 * Searches $_SESSION for the given key(s)
	 * @param array/string/object $keys
	 * @param boolean $run_session_start - Run session_start() before retrieval?
	 * @param boolean $throw_exception_when_cant_be_searched - throw exception when unsearchable?
	 * @throws NotAnArrayException
	 * @return string - the value | null
	 */
	public function session(
			$keys, 
			$run_session_start = MISSILESILO_GENERAL_UTILITIES__DEFAULT_EXECUTE_SESSION_START_ON_SESSION_GET, 
			$throw_exception_when_cant_be_searched = MISSILESILO_GENERAL_UTILITIES__DEFAULT_THROW_EXCEPTION_IN_REQUEST_SEARCH)
	{
		if($run_session_start)
			session_start();
		
		if(!is_array($_SESSION)){
			if($throw_exception_when_cant_be_searched) throw new NotAnArrayException('$_SESSION is not an array');
			return null;
		}
		
		if($this->is_str($keys))
			return $this->getArrayValue($_SESSION, [$keys]);
		else{
			if(is_array($keys))
				return $this->getArrayValue($_SESSION, $keys);
			else{
				if($throw_exception_when_cant_be_searched)
					throw new NotAnArrayException('$keys is neither a string nor an array');
				return null;
			}
		}
	}
	
	/* ===========================================================================
	 * END: HTTP request information
	 * ===========================================================================
	 */
	
	/* ==============================================================================================
	 * START: Special
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Stuff that doesn't really fit in anywhere else right now
	 * ==============================================================================================
	 */
	
	/**
	 * Cleans the $_POST array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_post_array()
	{
		array_walk_recursive($_POST, [$this, 'strip_slashes']);
	}
	
	/**
	 * Cleans the $_REQUEST array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_request_array()
	{
		array_walk_recursive($_REQUEST, [$this, 'strip_slashes']);
	}
	
	/**
	 * Cleans the $_GET array of quotes - use if magic quotes isn't enabled
	 */
	public function clean_get_array()
	{
		array_walk_recursive($_GET, [$this, 'strip_slashes']);
	}
	
	/**
	 * Gets just the username of a valid email address
	 * @param string $email
	 * @return boolean|string
	 */
	public function get_email_username($email)
	{
		$sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$email_as_address_is_okay = $sanitized_email?true:false;
		if(!$email_as_address_is_okay) return false;
		
		list($user, $domain) = explode('@', $sanitized_email);
		return $user;
	}
	
	/**
	 * Searches $data for any keys found in $keys
	 * @param array $data - the array to be searched
	 * @param array $keys - the keys to be searched for - values in order of priority
	 * @return mixed (seriously, it could be anything you can store in an array)
	 */
	public function getArrayValue(array &$data, array $keys)
	{
		if(!sizeof($keys)) return null;
		if(!is_array($data)) return null;
		$top_key = array_shift($keys);
	
		if(array_key_exists($top_key, $data))
		{
			return $data[$top_key];
		}else{
			return (sizeof($keys)?$this->getArrayValue($data, $keys):null);
		}
	}
	
	public function auto_require_php_files($directory, $once = true)
	{
		$it = iterator_to_array(new \GlobIterator("{$directory}/*.php*", \GlobIterator::CURRENT_AS_PATHNAME));
		foreach($it as $file)
			if($once)
				require_once($file);
			else
				require($file);
	}
	
	/* ===========================================================================
	 * END: Special
	 * ===========================================================================
	 */
	
	/* ==============================================================================================
	 * START: Log related
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * Updated On: 8/12/2014
	 * Stuff related to writing to and reading from a log
	 * ---------------------------------------------------------------
	 * + 8/12/2014 - Added ability to retrieve the log file's path
	 * ==============================================================================================
	 */

    /**
     * Grabs the location of the log file
     * @param bool $absolute
     * @return string
     */
	public function get_log_file_location($absolute = true)
	{
		if($absolute)
			return realpath($this->config->log_location);
		else
			return $this->config->log_location;
	}

    /**
     * Reads the last $number_of_lines from the log file
     * @param int $number_of_lines
     * @return array
     */
	public function read_last_x_lines_of_log($number_of_lines = 10)
	{
		if(!$this->config->allow_write_to_log) return null;
		$fp = fopen($this->config->log_location, 'r');
	
		$idx   = 0;
		$lines = array();
		while(($line = fgets($fp)))
		{
			$lines[$idx] = $line;
			$idx = ($idx + 1) % $number_of_lines;
		}
	
		$p1 = array_slice($lines,    $idx);
		$p2 = array_slice($lines, 0, $idx);
		$ordered_lines = array_merge($p1, $p2);
	
		fclose($fp);
	
		return $ordered_lines;
	}

    /**
     * Writes a line to the log file with a preceeding timestamp
     * @param $line
     * @return int|null
     */
	public function write_to_log($line)
	{
		if(!$this->config->allow_write_to_log) return null;
		
		return file_put_contents(
				$this->config->log_location,					//File to write to
				date(DATE_ATOM)."\t$line\n",					//Line to write
				FILE_APPEND | LOCK_EX);							//Options
	}

    /**
     * Encode $to_encode to json, write json to log file, return json
     * @param $to_encode
     * @return string
     */
	function log_and_return_json($to_encode){
		$json_encoded = json_encode($to_encode);
		$this->write_to_log("JSON RETURNED: $json_encoded");
		return $json_encoded;
	}

    /**
     * Writes $_REQUEST nvp set to log with each key/value set being written as a new line
     */
	function write_request_fields_to_log()
	{
		//Get the maximum field length
		$max_length = 0;
		foreach($_REQUEST as $key=>$value) if(strlen($key) > $max_length) $max_length = strlen($key);
		
		//Add the fields to the log
		foreach($_REQUEST as $key=>$value){
			if(is_array($value))
				$valuep = "array(".sizeof($value).")";
			else
				$valuep = $value;
			
			$key_pad = str_pad($key, $max_length);
			$this->write_to_log("    $key_pad => $valuep");
		}
	}
	
	/* ===========================================================================
	 * END: Log related
	 * ===========================================================================
	 */
}