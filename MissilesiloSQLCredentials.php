<?php

final class MissilesiloMySQLCredentials
{
	private $host;
	private $user;
	private $pass;
	private $db;
	
	public function __construct($host, $user, $pass, $db)
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	public function getHost()
	{
		return $this->host;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function getPass()
	{
		return $this->pass;
	}
	
	public function getDB()
	{
		return $this->db;
	}

}