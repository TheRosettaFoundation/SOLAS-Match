<?php
/*
 * MySQLHandler :: mySQL Wrapper. Version 1.3
 */

class MySQLHandler {
	private $DATABASE;
	private $USERNAME;
	private $PASSWORD;
	private $SERVER;

	private $LOGFILE = ''; 
	private $LOGGING = false; // debug on or off
	private $SHOW_ERRORS = false; // output errors. true/false
	private $SHOW_SQL = false; // turn off for production version. shows the sql statement that errored.
	private $USE_PERMANENT_CONNECTION = false;

	// Do not change the variables below
	private $CONNECTION;
	private $FILE_HANDLER;
	private $ERROR_MSG = '';
	private $SQL_ERRORED = '';
	var $ANALYSE_QUERIES = false;
	var $RECORDED_EXPLAINS = '';
	private $RETURN_XML;
	
	function MySQLHandler($return_xml = false)
	{
		$this->RETURN_XML = $return_xml;
		$settings = new Settings();
		$this->DATABASE = $settings->get('db.database');
		$this->USERNAME = $settings->get('db.username');
		$this->PASSWORD = $settings->get('db.password');
		$this->SERVER = $settings->get('db.server');
		$this->LOGFILE = $settings->get('db.log_file'); // full path to debug LOGFILE. Use only in debug mode!
		$this->LOGGING = (strlen($this->LOGFILE)>0) ? true : false;
		$this->SHOW_ERRORS = ($settings->get('db.show_errors') == 'y') ? true : false;
		$this->SHOW_SQL = ($settings->get('db.show_sql') == 'y') ? true : false;
		$this->ANALYSE_QUERIES = ($settings->get('db.analyse_queries') == 'y') ? true : false;
	}
	
	###########################################
	# Function:    init
	# Parameters:  N/A
	# Return Type: boolean
	# Description: initiates the MySQL Handler
	###########################################
	function init()
	{
		$ret = false;
		$this->logfile_init();
		if ($this->OpenConnection())
		{
			$ret = true;
		}
		else
		{
		  	$ret = false;
		}
		// Unset connection details for security
		$this->DATABASE = false;
		$this->USERNAME = false;
		$this->PASSWORD = false;		  	
		return $ret;
	}

	function initSelectDB($db, $username, $password) {
		$this->DATABASE = $db;
		$this->USERNAME = $username;
		$this->PASSWORD = $password;
		$this->init();
	}

	###########################################
	# Function:    OpenConnection
	# Parameters:  N/A
	# Return Type: boolean
	# Description: connects to the database
	###########################################
	function OpenConnection()
	{
		if ($this->USE_PERMANENT_CONNECTION) {
		  $conn = mysql_pconnect($this->SERVER,$this->USERNAME,$this->PASSWORD);
		} else {
		  $conn = mysql_connect($this->SERVER,$this->USERNAME,$this->PASSWORD);
		}
		if ((!$conn) || (!mysql_select_db($this->DATABASE,$conn))) {
		  $this->ERROR_MSG = "\r\n" . "Unable to connect to database - " . date('H:i:s');
		  $this->debug();
		  return false;
		} else {
			mysql_set_charset('utf8', $conn);
			$this->CONNECTION = $conn;
			return true;
		}
	}

	###########################################
	# Function:    CloseConnection
	# Parameters:  N/A
	# Return Type: boolean
	# Description: closes connection to the database
	###########################################
	function CloseConnection()
	{
	  	if (mysql_close($this->CONNECTION)) {
		  return true;
		} else {
		  $this->ERROR_MSG = "\r\n" . "Unable to close database connection - " . date('H:i:s');
		  $this->debug();
		  return false;
		}
	}

	###########################################
	# Function:    logfile_init
	# Parameters:  N/A
	# Return Type: N/A
	# Description: initiates the logfile
	###########################################
	function logfile_init() {
		if ($this->LOGGING) {
		  $this->FILE_HANDLER = fopen($this->LOGFILE,'a') ;
	  	  $this->debug();
		}
	}

	###########################################
	# Function:    logfile_close
	# Parameters:  N/A
	# Return Type: N/A
	# Description: closes the logfile
	###########################################
	function logfile_close() {
		if ($this->LOGGING) {
	  		if ($this->FILE_HANDLER) {
	  		  fclose($this->FILE_HANDLER) ;
	  	  }
		}
	}

	###########################################
	# Function:    debug
	# Parameters:  N/A
	# Return Type: N/A
	# Description: logs and displays errors
	###########################################
	function debug() {
		if ($this->SHOW_ERRORS) {
		  echo $this->ERROR_MSG;
		  if (strlen($this->SQL_ERRORED) > 0)
		  {
		  	echo "<br />" . $this->SQL_ERRORED;
		  }
		}
		if ($this->LOGGING) {
			if ($this->FILE_HANDLER) {
				fwrite($this->FILE_HANDLER,$this->ERROR_MSG);
			} else {
				return false;
			}
		}
	}

	###########################################
	# Function:    Insert
	# Parameters:  table : tablename; values : array of keys and values.
	# Return Type: boolean
	# Description: Generates insert query to send on to insertStr().
	###########################################
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
	
	function insertStr($sql)
	{
		$ret = false;
		if ((empty($sql)) || (stripos($sql, 'insert')!=0) || (empty($this->CONNECTION))) {
		  $this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not an INSERT - " . date('H:i:s');
		  $this->SQL_ERRORED = $sql;
		  $this->debug();
		}
		else
		{
			$conn = $this->CONNECTION;
			$results = mysql_query($sql,$conn);
			if (!$results) {
				$this->ERROR_MSG = "\r\n" . mysql_error()." - " . date('H:i:s');
				$this->SQL_ERRORED = $sql;
				$this->debug();
			} else {
				$result = mysql_insert_id();
				if ($result > 0)
				{
					$ret = $result;
				}
		  	}
		}
		return $ret;
	}

	###########################################
	# Function:    Select
	# Parameters:  sql : string
	# Return Type: array
	# Description: executes a SELECT statement and returns a
	#              multidimensional array containing the results
	#              array[row][fieldname/fieldindex]
	###########################################
	function Select($sql)
	{
		if ($this->SHOW_SQL)
		{
			$this->showSQL($sql);
		}
		if ((empty($sql)) || (stripos($sql, 'select')!=0) || (empty($this->CONNECTION)))
		{
			$this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not a SELECT - " . date('H:i:s');
			$this->SQL_ERRORED = $sql;
			$this->debug();
			return false;
		}
		else
		{
			$conn = $this->CONNECTION;
			$results = mysql_query($sql,$conn);
			if ($this->ANALYSE_QUERIES)
			{
				$this->recordExplain($sql);
			}
			if ((!$results) || (empty($results)))
			{
				$this->ERROR_MSG = "\r\n" . mysql_error()." - " . date('H:i:s');
				$this->SQL_ERRORED = $sql;
		  		$this->debug();
				return false;
			}
			else
			{
				$i = 0;
				$data = array();
				if ($this->RETURN_XML) {
					$data = $this->getXMLString($results);
				} else {
					while ($row = mysql_fetch_array($results)) {
						$data[$i] = $row;
						$i++;
					}
				}
				mysql_free_result($results);
				return empty($data) ? false : $data;
			}
		}
	}

	function recordExplain($q)
	{
		$s = '';
		$conn = $this->CONNECTION;
		//$result = mysql_query($q,$conn);
		if ($result = mysql_query('EXPLAIN '.$q, $conn))
		{
			$s .= '<pre>';
			$widths = array // Column names and widths (number of spaces)
				(    'Field'   => 20
				,    'Type'    => 12
				,    'Null'    => 4
				,    'Key'     => 3
				,    'Default' => 7
				,    'Extra'   => 14
			);
			$margin = 2; // Number of spaces between columns
			// Displays column headings
			foreach ($widths as $field => $width)
			{
				/*
				 * Limits the width of the column. If the contents are wider than the
				 * column, the full contents can be displayed by hovering the mouse
				 * over the cell
				 */
				$s .= '<span title="'.$field.'">';
				if (strlen($field) > $width)
				    $field = substr($field, 0, $width - 1).'…';
				$s .= '<b>'.str_pad($field, $width + $margin, ' ', STR_PAD_RIGHT).'</b></span>';
			}
			$s .= "\n";
			// Displays a row for each explained field containing name, type, etc.
			while ($row = mysql_fetch_assoc($result))
			{
				// Displays each column for the current row
				foreach ($widths as $field => $width)
				{
				    // Similar to the previous foreach loop
				    $s .= '<span title="'.$row[$field].'">';
				    if (strlen($row[$field]) > $width)
				        $row[$field] = substr($row[$field], 0, $width - 1).'…';
				    $s .= str_pad($row[$field], $width + $margin, ' ', STR_PAD_RIGHT).'</span>';
				}
				$s .= "\n";
			}
			$s .= '</pre>';
			$this->RECORDED_EXPLAINS .= $s;
		}
	}

	###########################################
	# Function:    Count Select
	# Parameters:  sql : string
	# Return Type: array
	# Description: returns an int value for the result
	#			of an, e.g., count(*) statement.
	###########################################
	function CountSelect($sql)
	{
		if ((empty($sql)) || (stripos($sql, 'select')!=0) || (empty($this->CONNECTION))) {
			$this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not a SELECT - " . date('H:i:s');
			$this->SQL_ERRORED = $sql;
			$this->debug();
			return false;
		}
		else
		{
			$conn = $this->CONNECTION;
			$results = mysql_query($sql,$conn);

			if ((!$results) || (empty($results))) {
				$this->ERROR_MSG = "\r\n" . $sql . mysql_error()." - " . date('H:i:s');
				$this->SQL_ERRORED = $sql;
		  $this->SQL_ERRORED = $sql;
		  		$this->debug();
				return false;
			}
			else
			{
				$data = 0;
				if ($this->RETURN_XML) {
					$data = $this->getXMLString($results);
				}
				else
				{
					$data = mysql_num_rows($results);
				}
				mysql_free_result($results);
				return $data;
			}
		}
	}

	###########################################
	# Function:    Update
	# Parameters:  sql : string
	# Return Type: true or false (Eoin)
	# Description: executes a UPDATE statement
	#              and returns number of affected rows
	# Note: if no rows were updated because the values were identical,
	# then a value of 0 is returned.
	###########################################
	function Update($sql)
	{
		$ret = false;
		if ((empty($sql)) || (stripos($sql, 'update')!=0)  || (empty($this->CONNECTION)))
		{
			$this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not an UPDATE - " . date('H:i:s');
			$this->debug();
		}
		else
		{
			$conn = $this->CONNECTION;
		  	$results = mysql_query($sql,$conn);
			if (!$results)
			{
				$this->ERROR_MSG = "\r\n" . mysql_error()." - " . date('H:i:s');
				$this->SQL_ERRORED = $sql;
			  	$this->debug();
			}
			else
			{
				$ret = (mysql_affected_rows() != -1);
			}
		}
		return $ret;
	}

###########################################
# Function:    Replace
# Parameters:  sql : string
# Return Type: boolean
# Description: executes a REPLACE statement
###########################################
	function Replace($sql) {
		if ((empty($sql)) || (stripos($sql, 'replace')!=0) || (empty($this->CONNECTION))) {
      $this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not a REPLACE - " . date('H:i:s');
      $this->debug();
      return false;
    } else {
	  	$conn = $this->CONNECTION;
  		$results = mysql_query($sql,$conn);
		  if (!$results) {
        $this->ERROR_MSG = "\r\n" . "Error in SQL Statement : ($sql) - " . date('H:i:s');
        $this->SQL_ERRORED = $sql;
	  	$this->debug();
        return false;
      } else {
    		return true;
      }
    }
	}

###########################################
# Function:    Delete
# Parameters:  sql : string
# Return Type: boolean
# Description: executes a DELETE statement
###########################################
	function Delete($sql)	{
		if ((empty($sql)) || (stripos($sql, 'delete')!=0) || (empty($this->CONNECTION))) {
      $this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> or not a DELETE - " . date('H:i:s');
      $this->debug();
      return false;
    } else {
  		$conn = $this->CONNECTION;
	  	$results = mysql_query($sql,$conn);
		  if (!$results) {
        $this->ERROR_MSG = "\r\n" . mysql_error()." - " . date('H:i:s');
        $this->SQL_ERRORED = $sql;
	  	$this->debug();
        return false;
      } else {
    		return true;
      }
    }
	}

###########################################
# Function:    Query
# Parameters:  sql : string
# Return Type: boolean
# Description: executes any SQL Query statement
###########################################
	function Query($sql)	{
		if ((empty($sql)) || (empty($this->CONNECTION))) {
      $this->ERROR_MSG = "\r\n" . "SQL Statement is <code>null</code> - " . date('H:i:s');
      $this->debug();
      return false;
    } else {
  		$conn = $this->CONNECTION;
	  	$results = mysql_query($sql,$conn);
		  if (!$results) {
        $this->ERROR_MSG = "\r\n" . mysql_error()." - " . date('H:i:s');
        $this->SQL_ERRORED = $sql;
	  	$this->debug();
        return false;
      } else {
    		return true;
      }
    }
	}

	function getCharacterSet() {
		if (empty($this->CONNECTION)) {
			$this->ERROR_MSG = "\r\n" . "No connection open - " . date('H:i:s');
			$this->debug();
			return false;
		}
		else {
			$conn = $this->CONNECTION;
			return mysql_client_encoding($conn);
		}
	}

###########################################
# Function:    cleanseSQL
# Parameters:  sql : string
# Return Type: string
# Description: cleanes variable for SQL, so escapes quotation marks, etc.
###########################################
	function cleanseSQL($str) {
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string(strip_tags(trim($str)));
	}
	
	/*
	 * Allow to keep HTML tags.
	 */
	function cleanseHTML($str)
	{
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	// Alias function.
	function cleanse($str)
	{
		return $this->cleanseSQL($str);
	}
	
	// Return 'NULL' is value is false.
	function cleanseNull($str)
	{
		return (!$str) ? 'NULL' : $this->cleanseSQL($str);
	}

	function showSQL($q)
	{
		echo '<pre>'.$q.'</pre>';
	}
}
