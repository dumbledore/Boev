<?php
	include_once '_connect.php';
	@include_once '_cpl_active.php';
	include_once SYS_AUTHENTICATION;
	
	if (
		(defined('SITE_CPL_FREEZE') && SITE_CPL_FREEZE === TRUE) ||
		(!defined('SITE_CPL_ERROR') || SITE_CPL_ERROR === TRUE) # if there is error
	) {
		//code when CPL is down
		jump_to(URL_ADMIN.'index.php', 'gates.php: CPL is locked/down');
		exit;
	}
	 else
	{
		if (!file_exists('gtA_'.$_GET['page'].'.php'))
			$clear['page'] = 'dummy'; //links back to main
		else
			$clear['page'] = $_GET['page'];
		
		// allow execution of included files
		// and prevent their execution from outside
		define('ALLOW_EXECUTION', TRUE);
			
		//authenticate user
		$sess_state = check_session();
		if ($sess_state == SESS_UNAVAILABLE
			|| $sess_state >= SESS_FALSE_AUTHENCITY) //e.g. SESS_INVALID_PASSWORD, etc.
		{
			kill_session();
			jump_to(URL_ADMIN.'index.php', 'gates.php: authentication failed: '.$sess_state);
			exit();
		}
		
		if ($sess_state == SESS_KILLED_BY_GC)
		{
			exit_session();
			jump_to(URL_ADMIN.'index.php', 'gates.php: session killed by the garbage collector');
			exit();
		}
		
		if ($sess_state == SESS_KILLED)
		{
			$temp = ( (time() - ($_SESSION['accessed'] + SESS_LIFE_TIME)) / 60);
			exit_session();
			jump_to(URL_ADMIN.'index.php', 'gates.php: session killed '.$temp.' minutes ago');
			exit();
		}
		
		if ($sess_state < SESS_INVALID_STATE && $sess_state != SESS_OK)
		{
			define('REV_INLINE', TRUE);
			include('revalidate.php');
			remember_data($clear['page'], TRUE); //will remember even previous POST data
			jump_to(URL_ADMIN.'revalidate.php', 'gates.php revalidate session');
			exit();
		}
		
		# restore POST data
		if ($_SESSION['rev_page'] === $clear['page']
			&& $_SESSION['rev_isgate'] === TRUE)
		{
			if (isset($_SESSION['rev_data']))
			{
				$_POST = $_SESSION['rev_data']['post'];
			}
		}
		
		unset($_SESSION['rev_page']);
		unset($_SESSION['rev_isgate']);
		unset($_SESSION['rev_data']);
		
		//include GATE file
		include 'gtA_'.$clear['page'].'.php';
		
		if (DEBUG === TRUE)
			include PATH_ENGINE.'layout_debug.php';
	}
?>