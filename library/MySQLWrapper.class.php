<?php
class MySQLWrapper
{
	private $database;
	private $username;
	private $password;
	private $server;

	private $logfile = ''; 
	private $logging = false; // debug on or off
	private $show_errors = false; // output errors. true/false
	private $show_sql = false; // turn off for production version.
	private $use_permanent_connection = false;

	// Do not change the variables below
	private $connection;
	private $FILE_HANDLER;
	private $ERROR_MSG = '';
	private $sql_errored = ''; // Filled with the query that failed
	
	function MySQLWrapper()
	{
		// Set up the connection
		$settings = new Settings();
		$this->database = $settings->get('db.database');
		$this->username = $settings->get('db.username');
		$this->password = $settings->get('db.password');
		$this->server = $settings->get('db.server');
		$this->logfile = $settings->get('db.log_file'); // full path to debug logfile. Use only in debug mode!
		$this->logging = (strlen($this->logfile)>0) ? true : false;
		$this->show_errors = ($settings->get('db.show_errors') == 'y') ? true : false;
		$this->show_sql = ($settings->get('db.show_sql') == 'y') ? true : false;
	}
	
	/*
	 * Call init() on the new MySQLWrapper object in order to establish 
	 * the DB connection.
	 */
	function init()
	{
		$this->initLogfile();
		$ret = $this->openConnection();
		// Unset connection details for security
		$this->database = false;
		$this->username = false;
		$this->password = false;		  	
		return $ret;
	}

	/*
	 * If you have to connect to a non-default DB, call this function
	 * instead of init().
	 */
	function initSelectDB($db, $username, $password)
	{
		$this->database = $db;
		$this->username = $username;
		$this->password = $password;
		$this->init();
	}

	/*
	 * Connect to the database itself.
	 */
	function openConnection()
	{
		$conn = false;
		$ret = false;
		if ($this->use_permanent_connection)
		{
			$conn = mysql_pconnect($this->server,$this->username,$this->password);
		}
		else
		{
			$conn = mysql_connect($this->server,$this->username,$this->password);
		}
		if ((!$conn) || (!mysql_select_db($this->database, $conn)))
		{
			$this->error_msg = "\r\n" . "Unable to connect to database - " . date('H:i:s');
			$this->debug();
			$ret = false;
		}
		else
		{
			// Success!
			mysql_set_charset('utf8', $conn);
			$this->connection = $conn;
			$ret = true;
		}
		return $ret;
	}

	/*
	 * Initial the log file.
	 */
	function initLogfile()
	{
		if ($this->logging)
		{
			$this->file_handler = fopen($this->logfile,'a') ;
			$this->debug();
		}
	}

	/*
	 * Discrepency: this function is currently NOT called, as
	 * no database "close connection" call is made.
	 */
	function logfileClose()
	{
		if ($this->logging && $this->file_handler)
		{
	  		fclose($this->file_handler);
		}
	}

	/*
	 * Logs and displays errors.
	 * Prerequisite: initLogfile() should have been called by now.
	 */
	function debug()
	{
		// Spit out the error to the browsers - probably not very wise for a production system.
		if ($this->show_errors)
		{
			echo $this->error_msg;
			if (strlen($this->sql_errored) > 0)
			{
				echo '<br>' . $this->sql_errored;
			}
		}
		// Write to the logging file.
		if ($this->logging)
		{
			if ($this->file_handler)
			{
				fwrite($this->file_handler, $this->error_msg);
			}
			else
			{
				return false;
			}
		}
	}

	/*
	 * Given a SELECT statement in a string, execute it and return the result.
	 * Result is a multidimensional array containing the results array[row][fieldname/fieldindex]
	 */
	function Select($sql)
	{
		if ($this->show_sql)
		{
			$this->showSQL($sql);
		}
		if ((empty($sql)) || (stripos($sql, 'select')!=0) || (empty($this->connection)))
		{
			$this->error_msg = "\r\n" . "SQL Statement is <code>null</code> or not a SELECT - " . date('H:i:s');
			$this->sql_errored = $sql;
			$this->debug();
			return false;
		}
		else
		{
			$conn = $this->connection;
			$results = mysql_query($sql,$conn);
			if ((!$results) || (empty($results)))
			{
				$this->error_msg = "\r\n" . mysql_error(). '; ' . date('H:i:s');
				$this->sql_errored = $sql;
		  		$this->debug();
				return false;
			}
			else
			{
				$i = 0;
				$data = array();
				while ($row = mysql_fetch_array($results))
				{
					$data[$i] = $row;
					$i++;
				}
				mysql_free_result($results);
				return empty($data) ? false : $data;
			}
		}
	}

	/*
	 * Given the names of the table, and an array of key => value,
	 * insert those values into the database.
	 */
	function Insert($table, $values)
	{
		$q = '';
		if ((is_array($values)) && (count($values)>0))
		{
			$keys = implode(', ', array_keys($values));
			$vals = implode(', ', array_values($values));
			$q = 'INSERT INTO '.$this->cleanseSQL($table).'('.$keys.')'.' VALUES('.$vals.')';
		}
		return $this->insertStr($q);
	}
	
	/*
	 * Given an actual SQL insert query as a string, execute it.
	 */
	function insertStr($sql)
	{
		$ret = false;
		if ((empty($sql)) || (stripos($sql, 'insert')!=0) || (empty($this->connection)))
		{
			$this->error_msg = "\r\n" . 'SQL Statement is <code>null</code> or not an INSERT; ' . date('H:i:s');
			$this->sql_errored = $sql;
			$this->debug();
		}
		else
		{
			$conn = $this->connection;
			$results = mysql_query($sql, $conn);
			if (!$results)
			{
				$this->error_msg = "\r\n" . mysql_error(). '; ' . date('H:i:s');
				$this->sql_errored = $sql;
				$this->debug();
			} 
			else
			{
				$result = mysql_insert_id();
				if ($result > 0)
				{
					$ret = $result;
				}
				else
				{
					$ret = true;
				}
		  	}
		}
		return $ret;
	}

	/*
	 * Execute an UPDATE statement. Returns true if successful, else false.
	 */	
	function Update($sql)
	{
		$ret = false;
		if ((empty($sql)) || (stripos($sql, 'update')!=0)  || (empty($this->connection)))
		{
			$this->error_msg = "\r\n" . 'SQL Statement is <code>null</code> or not an UPDATE; ' . date('H:i:s');
			$this->debug();
		}
		else
		{
			$conn = $this->connection;
		  	$results = mysql_query($sql,$conn);
			if (!$results)
			{
				$this->error_msg = "\r\n" . mysql_error()." - " . date('H:i:s');
				$this->sql_errored = $sql;
			  	$this->debug();
			}
			else
			{
				$ret = (mysql_affected_rows() != -1);
			}
		}
		return $ret;
	}

	/*
	 * Cleanes variable for SQL, so escapes quotation marks, etc.
	 */
	function cleanse($str)
	{
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}
		return mysql_real_escape_string(strip_tags(trim($str)));
	}
	
	/*
	 * If the string isn't empty, cleanse it. Otherwise, return NULL to
	 * be included directly in an SQL query.
	 */
	function cleanseNull($str)
	{
		return (!$str) ? 'NULL' : $this->cleanse($str);
	}

	function showSQL($q)
	{
		echo '<pre>'.$q.'</pre>';
	}
}
