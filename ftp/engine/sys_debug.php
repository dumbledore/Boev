<?php
	include_once '_settings.php';
	
	# Helper Functions
	
	function line_count($in) {
		$lines = explode("\n", $in);
		return count($lines);
	}
	
	function textarea($caption, $src) {
		echo '<h3>'.$caption.'</h3><br><textarea name="zaza" cols="64" rows="'.line_count($src).'">';
		echo $src;
		echo '</textarea><br><br>';
	}
	
	# Error reporting
	function buffer_error ($errno, $errstr, $errfile, $errline) {
		if (DEBUG === TRUE)
		{
			if ($errno == E_USER_ERROR)
			{
				send_error($errno, $errstr, $errfile, $errline);
				exit;
			}
			global $error_buffer;
			if (!isset($error_buffer))
				exit('Debugging is on, but error buffer is not set!');
				
			$errfile = explode('/', $errfile);
			$error_buffer[] = array($errno, $errstr.' in file '.$errfile[count($errfile)-1].' on line '.$errline);
		}
	}
	
	function send_error($errno, $errstr, $errfile, $errline) { //used for custom errors
		if ($errno == E_USER_ERROR)
		{
			
			$db_errno = mysql_errno();
			$db_error = mysql_error();
			
			include_once 'sys_dbc.php';
			unlock_tables(); //if locked by any chance
			
			include_once 'sys_mail.php';
			send_mail(MAIL_WEBMASTER_ADDRESS, 'Error report', wordwrap(
"
Error Report
--------------------------
Error: ".($errstr != '' ? $errstr : 'Unspecified Error')."
Site: ".ROOT."
File: ".$errfile."
Line: ".$errline."

".($db_errno != 0 ? "DB Error Log: ".$db_errno.': '.$db_error : '')
			));
			panel_working(false); # disable CPL
			
			include_once 'func_showmsg.php';
			showmsg('generic_error', URL_ADMIN.'index.php');
			
			exit;
		}
	}
	
	function debug_variable($myvar, $caption = 'variable') {
		if (DEBUG === TRUE)
		{
			global $dbgmsg_buffer;
			if (!isset($dbgmsg_buffer))
				exit('Debugging is on, but message buffer is not set!');
			$dbgmsg_buffer[] = array($caption, var_export($myvar, true));
		}
	}
	
	function dbg() {
		$vars = func_get_args();
		
		for ($i = 0; $i < count($vars); $i++)
			dbg_private($vars[$i]);
	}
	
	function dbg_private($var) {
		$res = var_export($var, true);
		echo '<br><textarea cols="64" rows="'.line_count($res).'">'.$res.'</textarea><br>';
	}
	
	function dbg_php() {
		$res = "";
		
		if (count($error_buffer) == 0)
			$res = "No errors.";
		else
			for ($i=0; $i < count($error_buffer) && $i < 10; $i++)
				$res .= $error_buffer[$i][0].': '.$error_buffer[$i][1]."\r\n";
		
		echo '<br><textarea cols="64" rows="'.line_count($res).'">'.$res.'</textarea><br>';
	}
	
	if (DEBUG === TRUE)
	{
		$error_buffer = array();
		set_error_handler('buffer_error');
		$dbgmsg_buffer = array();
	}
	 else
	{
		set_error_handler('send_error');
	}
?>