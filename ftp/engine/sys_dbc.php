<?php
	include_once 'sys_debug.php';
	
	# Globals, edited at runtime
	$DB_TABLES_LOCKED = false;
	$LAST_ERROR_QUERY = '';
	
	# Connect
	$link = @mysql_connect('localhost', 'boev', 'b000=v');
	
	if (!$link)
		trigger_error('Could not connect to DB', E_USER_ERROR);

	$dbname = 'boev';
	$db_selected = mysql_select_db($dbname, $link);
	
	if (!$db_selected)
		trigger_error('Could not select DB', E_USER_ERROR);
	
	if (DB_FIX_CHARSET === TRUE)
		query('SET NAMES \'cp1251\'');
	
	function query($myquery, $debug = false) {
		if (!$debug)
			return mysql_query($myquery);
		
		echo 'Query: '.$myquery.'<br><br>';
		return true;
	}
	
	# Lock tables for writing
	# Accepts variable number of arguments
	# Arguments can also be 1-dimentional arrays
	
	function lock_tables() {
		$tbls = func_get_args();
		$rbls_new = array();
		
		for ($i = 0; $i < count($tbls); $i++)
			if (!is_array($tbls[$i]))
				$tbls_new[] = '`'.$tbls[$i].'` WRITE';
			 else
			{
				for ($j = 0; $j < count($tbls[$i]); $j++)
					$tbls_new[] = '`'.$tbls[$i][$j].'` WRITE';
			}
		
		if (count($tbls_new) > 0)
			if (!query('LOCK TABLES '.implode(', ', $tbls_new).';'))
				trigger_error('Could not lock tables('.implode(', ', $tbls_new).'): '.mysql_errno().': '.mysql_error().'', E_USER_ERROR);

		global $DB_TABLES_LOCKED;
		$DB_TABLES_LOCKED = true;
		
		return true;
	}
	
	function unlock_tables() {
		global $DB_TABLES_LOCKED;
		if ($DB_TABLES_LOCKED)
		{
			if (!query('UNLOCK TABLES;'))
				trigger_error('Could not unlock tables: '.mysql_errno().': '.mysql_error().'', E_USER_ERROR);
			
			$DB_TABLES_LOCKED = FALSE;
			return true;
		}
	}
	
	function table_empty($table) {
		$res = query('SELECT 1 FROM `'.$table.'` LIMIT 1;');
		if ($res === false)
		{
			if (mysql_errno() == 1146)
				return false; //TABLE DOES NOT EXIST!
			
			trigger_error('Could not check if table `'.$table.'` is empty', E_USER_ERROR);
		}	
		
		
		return (mysql_num_rows($res) == 0);
	}
?>