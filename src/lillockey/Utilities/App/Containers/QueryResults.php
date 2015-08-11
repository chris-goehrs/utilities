<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 8/10/2015
 * Time: 10:01 AM
 */

namespace lillockey\Utilities\App\Containers;

/**
 * Class QueryResults
 *
 * Used for storing the query results to be passed around within the DB class
 *
 * @package lillockey\Utilities\App\Containers
 */
class QueryResults
{
	private $query;
	private $arguments;
	private $db;
	private $exec;
	private $statement;
	private $exception;
	private $id;

	/**
	 * @param string          $query
	 * @param array           $arguments
	 * @param \PDO            $db
	 * @param boolean         $exec
	 * @param \PDOStatement   $statement
	 * @param int             $id
	 * @param \Exception|null $exception
	 */
	public function __construct($query, array &$arguments, \PDO &$db, $exec, \PDOStatement &$statement, $id = 0, \Exception $exception = null)
	{
		$this->query = $query;
		$this->arguments = $arguments;
		$this->db = $db;
		$this->exec = $exec;
		$this->statement = $statement;
		$this->id = $id;
		$this->exception = $exception;
	}

	/**
	 * The string query formed for this executed query
	 * @return string
	 */
	public function query()
	{
		return $this->query;
	}

	/**
	 * The arguments used for named variables
	 * @return array
	 */
	public function &arguments()
	{
		return $this->arguments;
	}

	/**
	 * The instance of PDO (by reference) which was used to create the statement to execute the query
	 * @return \PDO
	 */
	public function &db()
	{
		return $this->db;
	}

	/**
	 * Wether or not the statement executed without errors
	 * @return bool
	 */
	public function exec()
	{
		return $this->exec;
	}

	/**
	 * The statement used for executing this query and returning the results
	 * @return \PDOStatement
	 */
	public function &statement()
	{
		return $this->statement;
	}

	/**
	 * The database exception
	 * @return \Exception|null
	 */
	public function &exception()
	{
		return $this->exception;
	}

	/**
	 * The insertion id related to this query (0 if no id used)
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}
}