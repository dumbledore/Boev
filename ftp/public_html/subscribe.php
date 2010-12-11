<?php
	include '_connect.php';
	
	if (isset($_COOKIE[SESS_NAME]))
	{
		include_once SYS_AUTHENTICATION;
		include_once SYS_DB_CONNECT;
		
		$sess_state = check_session();
		
		if ($sess_state == SESS_UNAVAILABLE
			|| $sess_state >= SESS_FALSE_AUTHENCITY) //e.g. SESS_INVALID_PASSWORD, SESS_INVALID_IP, etc.
		{
			//if attempting to hijack, just kill the session
			kill_session();
		}
		
		if ($sess_state == SESS_KILLED)
		{
			//if the user has left closed the browser
			//without signing out long ago,
			//peacefully exit the session
			exit_session();
		}
		
		if ($sess_state < SESS_INVALID_STATE)
		//No hijacking here. No more test are neccessary
		//(in contrast to those used in the CPL)
		{
			#Here is the code
			if ($_GET['mode'] == 1)
				$mode = 1;
			else
				$mode = 0;
			
			if (id_verify($_GET['page']))
			{
				lock_tables('cm_struct_view');
				
				$res = query('SELECT `subscribed` FROM `cm_struct_view` WHERE `id` = \''.$_GET['page'].'\';');
				# The mode is not tested, which officially should be a vulnerability, but there is an extremely small chance of mischief
				if ($res !== false)
				{
					if (mysql_num_rows($res) != 0)
					{
						# found page
						$row = mysql_fetch_assoc($res);
						
						if ($row['subscribed'] != '')
							$subscribed = explode(';', $row['subscribed']);
						else
							$subscribed = array();
						
						$already_there = false;
						for ($i = 0; $i < count($subscribed); $i++)
						{
							if ($mode == 0 && $subscribed[$i] == $_SESSION['username']) # delete
								unset($subscribed[$i]);
							
							if ($mode == 1 && $subscribed[$i] == $_SESSION['username']) #already there!
							{
								$already_there = true;
								break;
							}
						}
						
						if ($mode == 1 && !$already_there) # i.e. to be subscribed
							$subscribed[] = $_SESSION['username'];
						
						#return result into DB
						
						query('UPDATE `cm_struct_view` SET `subscribed` = \''.implode(';', $subscribed).'\' WHERE `id` = \''.$_GET['page'].'\';');
					}
				}
				unlock_tables();
			}
		}
	}

	jump_to('index.php?page='.$_GET['page']);
?>