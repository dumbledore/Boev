<?php
	include_once 'sys_debug.php';
	
	if (!defined('ALLOW_EXECUTION')) {
		jump_to(URL_SITE.'index.php', 'sys_allowexec.php: execution not allowed'); //just stop the script
		exit;
	}
	
	if (defined('ACCESS_LEVEL'))
	{
		//check data validity
		if (
			ACCESS_LEVEL != 'admin' &&
			ACCESS_LEVEL != 'editor' &&
			ACCESS_LEVEL != 'viewer'
		) {
			jump_to(URL_SITE.'index.php', 'sys_allowexec.php: ACCESS_LEVEL is wrong');
			exit;
		}
		
		//check session validity
		if (isset($_SESSION['credentials']))
		{
			//check data validity
			if (
				$_SESSION['credentials'] != 'admin' &&
				$_SESSION['credentials'] != 'editor' &&
				$_SESSION['credentials'] != 'viewer'
			) {
				jump_to(URL_SITE.'index.php', 'sys_allowexec.php: $_SESSION[\'credentials\'] is wrong');
				exit;
			}
			
			//ACCESS LEVEL is set and SESSION is OK
			
			//check access level
			switch (ACCESS_LEVEL)
			{
				//CHECK ADMIN RULES
				case 'admin':
					if ($_SESSION['credentials'] != 'admin')
					{
						jump_to(URL_SITE.'index.php', 'sys_allowexec.php: A non-admin user trying to pass as admin');
						exit;
					}
					break;
				
				//CHECK EDITOR RULES
				case 'editor':
					if (
						$_SESSION['credentials'] != 'admin' &&
						$_SESSION['credentials'] != 'editor'
					) {	
						jump_to(URL_SITE.'index.php', 'sys_allowexec.php: A non-admin/editor user trying to pass as such');
						exit;
					}
				
				//No need to check viewers, so all is well!
			}
		}
		 else
		{
			jump_to(URL_SITE.'index.php', 'sys_allowexec.php: $_SESSION_credentials not set');
			exit;
		}
	}
?>