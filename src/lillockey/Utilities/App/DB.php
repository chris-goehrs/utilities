<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 9:53 AM
 */

namespace lillockey\Utilities\App;


use lillockey\Utilities\App\Containers\QueryResults;
use lillockey\Utilities\Config\AbstractCustomConfig;
use lillockey\Utilities\Config\DefaultCustomConfig;
use lillockey\Utilities\Exceptions\DatabaseConnectionTypeException;
use lillockey\Utilities\Exceptions\DatabaseCredentialValidationException;

class DB extends AbstractUtility
{
	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 1
	//      Config
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////
	private $config;

	/**
	 * @param AbstractCustomConfig      $config - The configuration to use when grabbing the database
	 * @param null                      $name - The name under which this was stored
	 */
	public function __construct(AbstractCustomConfig $config = null, $name = null)
	{
		if($config == null)
			$config = new DefaultCustomConfig();
		$this->config = $config;

		parent::__construct($name);
	}


	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	// SECTION 2
	//      SQL Database
	///////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////
	private $pdo_instance = null;

	/**
	 * Gets an instance of PDO based on the stored parameters
	 * @param bool $create_new_instance - Forces the creation of a new instance of PDO
	 * @return \PDO
	 * @throws DatabaseCredentialValidationException
	 * @throws DatabaseConnectionTypeException
	 */
	public function getPDO($create_new_instance = true)
	{
		//Check for existing instance
		if($this->pdo_instance != null && $create_new_instance != true && $this->pdo_instance instanceof \PDO) return $this->pdo_instance;

		//Check for credential issues
		if(!$this->config->db_credentials_are_valid())
			throw new DatabaseCredentialValidationException('There was a problem with the db credentials');

		if(!in_array($this->config->db_type, array('mysql', 'pgsql', 'cubrid', 'sybase', 'mssql', 'dblib', 'firebird', 'ibm', 'informix', 'oci', 'odbc', 'sqlite', 'sqlsrv', '4D')))
			throw new DatabaseConnectionTypeException("The connection type \"{$this->config->db_type}\" was not valid.");


		//Construct the options
		$host=		$this->config->host;
		$dbname=	$this->config->db;
		$username=	$this->config->user;
		$passwd=	$this->config->pass;
		$options=	array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);

		//Construct and return the new instance
		return ($passwd == ''
			?$this->pdo_instance = new \PDO("{$this->config->db_type}:host={$host}; dbname={$dbname}", $username, null, $options)
			:$this->pdo_instance = new \PDO("{$this->config->db_type}:host={$host}; dbname={$dbname}", $username, $passwd, $options));
	}

	/**
	 * Runs a raw query and returns the first column<br/>
	 * <strong>NOTE:</strong> Good for getting a count of records
	 * @param       $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @return string - the first column
	 */
	public function run_raw_query_and_return_first_column($query, array $arguments = null)
	{
		$statement = $this->run_raw_query_and_return_statement($query, $arguments);
		return $statement->fetchColumn();
	}

	/**
	 * Runs a raw SQL query and returns the first record found
	 * @param string $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @param int $pdo_fetch_style - [optional] pdo fetch style \PDO::FETCH_OBJ by default
	 * @param string $pdo_fetch_class_name - [optional] the class name (used only when PDO::FETCH_CLASS is used for $pdo_fetch_style
	 * @return array|\stdClass - the record found
	 */
	public function run_raw_query_and_return_first_record($query, array $arguments = null, $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class_name = '\\stdClass')
	{
		$statement = $this->run_raw_query_and_return_statement($query, $arguments);
		if($pdo_fetch_style == \PDO::FETCH_CLASS){
			$statement->setFetchMode($pdo_fetch_style, $pdo_fetch_class_name);
			$ob = $statement->fetch();
			$statement->closeCursor();
			return $ob;
		}else{
			$ob = $statement->fetch($pdo_fetch_style);
			$statement->closeCursor();
			return $ob;
		}
	}

	/**
	 * Runs a raw SQL query
	 * @param string $query - SQL Query
	 * @param array $arguments - [optional] array containing arguments for the query
	 * @param int $pdo_fetch_style - [optional] pdo fetch style \PDO::FETCH_OBJ by default
	 * @param string $pdo_fetch_class_name - [optional] the class name (used only when PDO::FETCH_CLASS is used for $pdo_fetch_style
	 * @return array - the records found
	 */
	public function run_raw_query_and_return_all_records($query, array $arguments = null, $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class_name = '\\stdClass')
	{
		$statement = $this->run_raw_query_and_return_statement($query, $arguments);
		if($pdo_fetch_style == \PDO::FETCH_CLASS){
			$statement->setFetchMode($pdo_fetch_style, $pdo_fetch_class_name);
			return $statement->fetchAll();
		}else{
			return $statement->fetchAll($pdo_fetch_style);
		}
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
		return $r->exec();
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
		return $r->statement();
	}

	/**
	 * Executes the selected query against the given arguments
	 * @param       $query
	 * @param array $arguments
	 * @return QueryResults
	 * @throws DatabaseCredentialValidationException
	 */
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

		$results = new QueryResults($query, $arguments, $db, $execute_results, $statement, $db->lastInsertId(), $exception);

		return $this->log_query($results);
	}

	private function write_to_log($to_write = '')
	{
		$log = InstanceHolder::log($this->name);
		$log->write_to_log($to_write);
	}

	private function log_query(QueryResults &$results)
	{
		if(!$this->config->db_log_queries) return $results;	//TODO: rework this



		$this->write_to_log('QUERY_EXECUTED!');
		$this->write_to_log('    Query: '. $results->query());
		if(is_array($results->arguments())){
			foreach($results->arguments() as $key => $value){
				$this->write_to_log("    Query Argument '$key' = '$value'");
			}
		}
		$this->write_to_log('  Execution Result: '.($results->exec()?'ok':'NOT OK!'));
		$this->write_to_log('  Insert ID Value: '.$results->id());
		$statement = $results->statement();
		if(!$results->exec()){
			assert($statement instanceof \PDOStatement);
			$err = $statement->errorInfo();
			$this->write_to_log('  !!Error Code: '. $err[0]);
			$this->write_to_log('  !!Driver Error Code: '. $err[1]);
			$this->write_to_log('  !!Driver Error Message: '. $err[2]);
		}
		if($results->exception() instanceof \Exception){
			$ex = $results->exception();
			$this->write_to_log('  Exception: '.$ex->getMessage().'\n'.$ex->getTraceAsString());
		}

		return $results;
	}

	private function build_where(array $where, $where_prefix = ' ', $variable_prefix = '_where_')
	{
		foreach($where as $wherefield=>$wherevalue){
			if($this->field_name_is_valid($wherefield) === false) return null;
		}

		$return_me = array(
			'query' => '',
			'array' => array()
		);

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
	public function select_all_distinct_by($table, $field, array $where = array(), $orderby = null, $asc_desc = 'ASC')
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
	 * @param        $table
	 * @param array  $where
	 * @param null   $orderby
	 * @param string $asc_desc
	 * @param int    $pdo_fetch_style
	 * @param null   $pdo_fetch_class
	 * @return array|null
	 */
	public function select_all($table, array $where = array(), $orderby = null, $asc_desc = 'ASC', $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class = null)
	{
		$table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return null;
		$ordertext = $this->build_order($orderby, $asc_desc);
		$wherear = $this->build_where($where);
		if($ordertext === null || $wherear === null) return null;

		$query = "SELECT * FROM `$table` {$wherear['query']}$ordertext";

		return $this->run_raw_query_and_return_all_records($query, $wherear['array'], $pdo_fetch_style, $pdo_fetch_class);
	}

	/**
	 * Limited select statement
	 * @param        $table
	 * @param array  $where
	 * @param null   $orderby
	 * @param string $asc_desc
	 * @param int    $offset
	 * @param null   $limit
	 * @param int    $pdo_fetch_style
	 * @param null   $pdo_fetch_class
	 * @return array|null
	 */
	public function select($table, array $where = array(), $orderby = null, $asc_desc = 'ASC', $offset = 0, $limit = null, $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class = null)
	{
		$table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return null;
		$ordertext = $this->build_order($orderby, $asc_desc);
		$wherear = $this->build_where($where);
		if($ordertext === null || $wherear === null) return null;
		$offset = intval($offset);

		$query = "SELECT * FROM `$table` {$wherear['query']}$ordertext";
		if($limit){
			$limit = intval($limit);
			$query .= " LIMIT $limit";
		}
		if($offset > 0){
			$offset = intval($offset);
			$query .= " OFFSET $offset";
		}

		return $this->run_raw_query_and_return_all_records($query, $wherear['array'], $pdo_fetch_style, $pdo_fetch_class);
	}

	/**
	 * Retrieves the first row in the table with $field matching $value
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $orderby [optional] if null, no ordering will be done
	 * @param string $asc_desc [optional] must be either ASC or DESC
	 * @return \stdClass|NULL
	 */
	public function select_one_by($table, $field, $value, $orderby = null, $asc_desc = 'ASC', $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class = null)
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
		return $this->run_raw_query_and_return_first_record($query, array('id' => $value), $pdo_fetch_style, $pdo_fetch_class);
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
	public function select_one_value_by($table, $field, array $where = array(), $orderby = null, $asc_desc = 'ASC')
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
	 * @param int	 $pdo_fetch_style [optional] PDO::FETCH_(type)
	 * @param string $pdo_fetch_class [optional] used to populate PDO::FETCH_CLASS
	 * @return NULL|array (stdObject)
	 */
	public function select_all_by($table, $field, $value, $orderby = null, $asc_desc = 'ASC', $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class = null)
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
		return $this->run_raw_query_and_return_all_records($query, array('id' => $value), $pdo_fetch_style, $pdo_fetch_class);
	}

	/**
	 * Retrieves all rows in the table ordered
	 * @param string $table - table to dump
	 * @param string $orderby - field to order by
	 * @param string $asc_desc [optional] - either 'ASC' or 'DESC'
	 * @param int    $pdo_fetch_style [optional]
	 * @param string $pdo_fetch_class [optional]
	 * @return array|NULL
	 */
	public function select_all_order($table, $orderby, $asc_desc = 'ASC', $pdo_fetch_style = \PDO::FETCH_OBJ, $pdo_fetch_class = null)
	{
		$table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return null;
		if($this->field_name_is_valid($orderby) === false) return null;
		if($asc_desc !== 'ASC' && $asc_desc !== 'DESC') return null;


		$query = "SELECT{$this->config->db_cache_text} * FROM `$table` ORDER BY $orderby $asc_desc";
		return $this->run_raw_query_and_return_all_records($query, null, $pdo_fetch_style, $pdo_fetch_class);
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
		$statement = $this->run_raw_query_and_return_statement($query, array('id' => $value));
		return intval($statement->fetchColumn(0));
	}

	/**
	 * This is left in there for backwards compatibility
	 * TODO: Remove on text major release
	 * @deprecated alias of DB::count
	 * @param        $table
	 * @param array  $where
	 * @param null   $orderby
	 * @param string $asc_desc
	 * @return int|null
	 */
	public function count_by_multi($table, array $where = array(), $orderby = null, $asc_desc = 'ASC')
	{
		return $this->count($table, $where);
	}

	/**
	 * Run a count against the given table
	 * @param string $table
	 * @param array  $where
	 * @return int|null
	 */
	public function count($table, array $where = array())
	{
		$table = $this->config->table($table);

		//Clean the questionable fields
		if($this->field_name_is_valid($table) === false) return null;
		$wherear = $this->build_where($where);
		if($wherear === null) return null;

		$query = "SELECT{$this->config->db_cache_text} COUNT(*) FROM `$table`{$wherear['query']}";
		$statement = $this->run_raw_query_and_return_statement($query, sizeof($wherear['array'])?$wherear['array']:null);
		return intval($statement->fetch(\PDO::FETCH_COLUMN, 0));
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
		$submitted_values = array();
		foreach($fields as $column=>$value)
		{
			if($this->field_name_is_valid($column) === false) {continue;}
			if(empty($value)) continue;

			if(InstanceHolder::util()->str_left_is($value, '#__')){
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
		return ($r->exec()?$r->id():false);
	}

	/**
	 * Runs an update statement against the table
	 * @param string $table
	 * @param array $where
	 * @param array $fields
	 * @return bool
	 */
	public function update($table, array $where, array $fields)
	{
		$table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return false;
		if(sizeof($fields) < 1) return false;

		//Prepare the basic variables
		$query = "UPDATE `$table` SET";		//The basic query string
		$i = 0;								//Counter for number of fields
		$submitted_fields = array();				//The fields that have been kept

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
	public function update_by($table, $id_field, $id_value, array $fields)
	{
		$table = $this->config->table($table);
		if($this->field_name_is_valid($id_field) === false) return false;
		if($this->field_name_is_valid($table) === false) return false;
		if(sizeof($fields) < 1) return false;

		//Prepare the basic variables
		$query = "UPDATE `$table` SET";		//The basic query string
		$i = 0;								//Counter for number of fields
		$submitted_fields = array();		//The fields that have been kept

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

	public function delete($table, array $where)
	{
		$table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return false;
		if(!sizeof($where)) return false;

		$wherear = $this->build_where($where);

		$query = "DELETE FROM `$table` {$wherear['query']}";
		return $this->run_raw_query_and_return_query_status($query, $wherear['array']);
	}

	/**
	 * Deletes the records where the field ($id_field) equals the value ($id_value)
	 * @param string $table
	 * @param string $id_field
	 * @param string $id_value
	 * @return boolean
	 */
	public function delete_by($table, $id_field, $id_value)
	{
		if($this->field_name_is_valid($table) === false) return false;
		if($this->field_name_is_valid($id_field) === false) return false;

		return $this->delete($table, array(
			$id_field => $id_value
		));
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

	/**
	 * Filters the column/table name to match MYSQL documentation
	 *
	 * Based on: http://stackoverflow.com/questions/4977898/check-for-valid-sql-column-name
	 * @param string $field
	 * @return mixed
	 */
	public function field_name_is_valid($field)
	{
		return filter_var($field, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '^[a-zA-Z_][a-zA-Z0-9_]*$^')));
	}
}