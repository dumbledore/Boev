<?php
	
	include_once '_settings.php';
	include_once 'sys_dbc.php';
	include_once 'sys_debug.php';
	include_once 'sys_mail.php';
	function initiate_session() {
		session_name(SESS_NAME);
		session_set_cookie_params(SESS_COOKIE_EXP_TIME, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		ini_set('session.save_path', PATH_SESSIONS);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.gc_maxlifetime', SESS_LIFE_TIME);
		session_start();
	}
	
	function create_session($userdata) {
		initiate_session();
		$_SESSION['ready'] = true;
		$_SESSION['username'] = $userdata['username'];
		$_SESSION['password'] = $userdata['password'];
		$_SESSION['email'] = $userdata['email'];
		$_SESSION['credentials'] = $userdata['credentials'];
		$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['accessed'] = time();
		$_SESSION['incpl'] = TRUE;
		if (SITE_ACCOUNTS === SITE_ACC_LOGGED_BEFORE)
			setcookie('LOGGED_BEFORE', 'yes', time() + 315360000, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN); //ten years, this is
	}
	
	function check_session() {
		//a wrapper for check_session_only
		//If bad things are done, it would notify the admin.
		
		$res = check_session_only();
		if ($res >= SESS_HIJACK_ATTEMPT)
		{
			$msg =
"
A hijacking attempt has been caught:

IP: ".$_SERVER['REMOTE_ADDR']."
Browser: ".$_SERVER['HTTP_USER_AGENT']."

Please, consider the situation.
";
			send_mail(MAIL_WEBMASTER_ADDRESS, 'HIJACKING ATTEMPT!', wordwrap($msg, 70));
		}
		
		return $res;
	}
	
		function check_session_only() {
		if (!isset($_COOKIE[SESS_NAME]))
			return SESS_UNAVAILABLE; //no active session
		
		initiate_session();
		
		if ($_SESSION['ready'] !== true)
			return SESS_KILLED_BY_GC;
		
		if ($_SESSION['IP'] !== $_SERVER['REMOTE_ADDR'])
			return SESS_INVALID_IP;
			
		if ($_SESSION['browser'] != $_SERVER['HTTP_USER_AGENT'])
			return SESS_INVALID_BROWSER;
		
		if ($_SESSION['accessed'] + SESS_LIFE_TIME < time())
			return SESS_KILLED;

		if (defined('SITE_HIGH_SECURITY_MODE') && SITE_HIGH_SECURITY_MODE === TRUE && $_SESSION['incpl'] !== TRUE)
			return SESS_NOT_IN_CPL;			

		if ($_SESSION['accessed'] + SESS_EXP_TIME < time())
			return SESS_EXPIRED;
			
		$_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['accessed'] = time(); //revalidate time
		return SESS_OK;
	}
	
	function regenerate_session($password) {
		if (!is_string($password))
			return SESS_INVALID_PASSWORD; //i.e. passed bad data
		
		if ($password == '')
			return SESS_INVALID_PASSWORD; //i.e. passed bad data, in this case: empty username/password
		
		if (strlen($password) > USER_PASS_MAX_LENGTH)
			return SESS_INVALID_PASSWORD; //i.e. passed bad data, in this case: too long username/password

		if (!isset($_COOKIE[SESS_NAME]))
			return SESS_UNAVAILABLE; //no active session
			
		initiate_session();
		
		if ($_SESSION['ready'] !== true)
			return SESS_KILLED_BY_GC;
		
		if ($_SESSION['IP'] !== $_SERVER['REMOTE_ADDR'])
			return SESS_INVALID_IP;
			
		if ($password !== $_SESSION['password'])
			return SESS_INVALID_PASSWORD;
			
		$_SESSION['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['accessed'] = time();
		$_SESSION['incpl'] = TRUE;
		return SESS_OK;
	}
	
	function exit_session() {
		//normal exit procedure
		if (isset($_COOKIE[SESS_NAME]))
		{
			initiate_session();
			session_destroy();
			setcookie(SESS_NAME, FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		}
	}
	
	function kill_session() {
		//used to kill SESSID on hackers PCs
		if (isset($_COOKIE[SESS_NAME]))
		{
			setcookie(SESS_NAME, FALSE, time() - 3600, PATH_MAIN, (COOKIE_DOMAIN ? '.' : '').DOMAIN);
		}
	}
?>