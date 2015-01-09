<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 9:49 PM
 */

namespace Missilesilo\Utilities\App\Traits;

use Missilesilo\Utilities\Exceptions\DatabaseCredentialValidationException;

trait TraitMySQL
{
    private $pdo_instance = null;

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
     * @param
     * @return array - all rows fetched by the query
     */

    /**
     * Runs a raw SQL query
     * @param string $query - SQL Query
     * @param array $arguments - [optional] array containing arguments for the query
     * @param int $pdo_fetch_style - [optional] pdo fetch style \PDO::FETCH_OBJ by default
     * @return array - the records found
     */
    public function run_raw_query_and_return_all_records($query, array $arguments = null, $pdo_fetch_style = \PDO::FETCH_OBJ)
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
     * @return \stdClass|NULL
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
        return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
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
}