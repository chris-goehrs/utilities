<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 9:53 AM
 */

namespace lillockey\Utilities\App;


use lillockey\Utilities\App\Containers\QueryResults;
use lillockey\Utilities\App\DBT\DBTQuery;
use lillockey\Utilities\Config\AbstractCustomConfig;
use lillockey\Utilities\Config\DefaultCustomConfig;
use lillockey\Utilities\Exceptions\DatabaseConnectionTypeException;
use lillockey\Utilities\Exceptions\DatabaseCredentialValidationException;

class DBT extends AbstractUtility
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

    /**
	 * Gets an instance of PDO based on the stored parameters
	 * @return \PDO
	 * @throws DatabaseCredentialValidationException
	 * @throws DatabaseConnectionTypeException
	 */
	public function getPDO()
	{
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
			? new \PDO("{$this->config->db_type}:host={$host}; dbname={$dbname}", $username, null, $options)
			: new \PDO("{$this->config->db_type}:host={$host}; dbname={$dbname}", $username, $passwd, $options));
	}


    /**
     * @param DBTQuery[] $queries
     * @return bool
     */
	public function executeTransaction(array $queries)
    {
        $pdo = $this->getPDO();

        try{
            $pdo->beginTransaction();

            foreach($queries as $query){
                $statement = $pdo->prepare($query->getQuery());
                if($query->getArguments() == null){
                    $statement->execute();
                }else{
                    $statement->execute($query->getArguments());
                }
            }

            $results = $pdo->commit();
            return $results;
        }catch(\PDOException $exception){
            try{
                $pdo->rollBack();
            }catch(\Exception $e){}
            return false;
        }
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

    /**
     * Constructs a new query container for use in a transaction
     * @param $table
     * @param array $fields
     * @return null|DBTQuery
     */
	public function insert($table, array $fields)
	{
		$table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return null;

		//Build column/value lists
		$columns = "";
		$values = "";
		$i = 0;
		$submitted_values = array();
		foreach($fields as $column=>$value)
		{
			if($this->field_name_is_valid($column) === false) {continue;}
			if($value === null) continue;

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
		if(sizeof($submitted_values) < 1) return null;

		//Create the query
		$query = "INSERT INTO `$table` ($columns) VALUES ($values);";

		//Run the query
        return new DBTQuery($query, $submitted_values);
	}

	/**
	 * Runs an update statement against the table
	 * @param string $table
	 * @param array $where
	 * @param array $fields
	 * @return null|DBTQuery
	 */
	public function update($table, array $where, array $fields)
	{
		$table = $this->config->table($table);
		if($this->field_name_is_valid($table) === false) return null;
		if(sizeof($fields) < 1) return null;

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
		if(sizeof($submitted_fields) < 1) return null;

		//Build WHERE clause
		$wherear = $this->build_where($where);
		if($wherear === null) return null;

		//Merge arrays together
		$final_ar = array_merge($submitted_fields, $wherear['array']);
		if(!is_array($final_ar)) return null;

		//Add where to the query
		$query .= $wherear['query'];

		//Execute query
		return new DBTQuery($query, $submitted_fields);
	}

    /**
     * @param $table
     * @param array $where
     * @return null|DBTQuery
     */
	public function delete($table, array $where)
	{
		$table = $this->config->table($table);

		if($this->field_name_is_valid($table) === false) return null;
		if(!sizeof($where)) return null;

		$wherear = $this->build_where($where);

		$query = "DELETE FROM `$table` {$wherear['query']}";
        return new DBTQuery($query, $wherear['array']);
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