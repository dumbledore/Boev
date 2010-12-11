<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin'); #ONLY ADMINS CAN USE IT!
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	//Check POST data
	if (
		!isset($_POST['um_username']) ||
		!isset($_POST['um_type']) ||
		!isset($_POST['um_email'])
	) {
		jump_to(URL_ADMIN.'main.php', 'gtA_um_add.php: invalid input data');
		exit;
	}
	
	$res = user_add($_POST['um_username'], $_POST['um_email'], $_POST['um_type']);

	# UPDATED CODE FOLLOWS
	if ($res != USER_ADD_OK)
	{
		switch ($res)
		{
			case USER_ALREADY_EXISTS:
				showmsg('user_exists', URL_ADMIN.'main.php?page=um_browse');
				exit;
			
			case USER_INVALID_NAME:
				showmsg('user_invalid_name', URL_ADMIN.'main.php?page=um_browse');
				exit;
				
			case USER_INVALID_MAIL:
				showmsg('user_invalid_mail', URL_ADMIN.'main.php?page=um_browse');
				exit;
			
			case USER_CANNOT_SEND_MAIL:
				showmsg('user_cannot_send_activation', URL_ADMIN.'main.php?page=um_browse');
				exit;
			
			default:
			trigger_error('Error when creating user: Error No.'.$res, E_USER_ERROR);
		}
	}
	# UPDATED CODE ENDS
	
	jump_to(URL_ADMIN.'main.php?page=um_browse');
?>