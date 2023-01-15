<?php
/* DATABASE CONNECT */

class Database {
    public $host;
    private $username;
    private $password;
    private $database;
    public $connection;
	
	function __construct() {
	    $this->host = MYSQL_DB_HOST;
	    $this->database = MYSQL_DB_DATABASE;
	    $this->username = MYSQL_DB_USERNAME;
	    $this->password = MYSQL_DB_PASSWORD;
	}

	function connect(): void {
		$this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
	}

	function disconnect(): void {
		mysqli_close($this->connection);
	}
}