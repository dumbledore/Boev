<?php
	include_once '_connect.php';
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	switch (user_activate_by_request($_GET['uuid']))
	{
		case USER_ACTIVATE_OK:
			//activated. show msg and redirect
			showmsg('user_activated', URL_SITE.'index.php');
			break;

		case USER_ACTIVATE_FAILED:
			showmsg('user_activate_failed', URL_SITE.'index.php');
			break;
		
		default:
			jump_to('index.php', 'hijack attempt on activate.php');
			break;
	}
?>