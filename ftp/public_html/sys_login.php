<?php
	if (SITE_ACCOUNTS >= SITE_ACC_ON)
	{
		if (isset($_COOKIE[SESS_NAME]))
		{
			include SYS_AUTHENTICATION;
			
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
				if (defined('SITE_HIGH_SECURITY_MODE') && SITE_HIGH_SECURITY_MODE === TRUE) //only care if in high security mode
					$_SESSION['incpl'] = FALSE;
				# $_SESSION['accessed'] = time(); session expiry time is checked only within the CPL
				define('LOGGED', TRUE);
				define('SHOW_BAR', TRUE);
			}
		}
		 else
		{
			switch (SITE_ACCOUNTS)
			{
				case SITE_ACC_ALWAYS:
					define('SHOW_BAR', TRUE);
					break;
				
				case SITE_ACC_LOGGED_BEFORE:
					if (isset($_COOKIE['LOGGED_BEFORE']))
						define('SHOW_BAR', TRUE);
					break;
			}
		}
	}
	 else
	{
		define('SHOW_BAR', FALSE);
	}
?>