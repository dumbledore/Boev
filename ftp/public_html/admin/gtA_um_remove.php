<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	//Check POST data
	if (
		!isset($_POST['um_selected'])
	) {
		jump_to(URL_ADMIN.'main.php?page=um_browse', 'invalid post data');
		exit;
	}

	$res = user_remove($_POST['um_selected']);
	
	if ($res != USER_OK)
	{
		jump_to(URL_ADMIN.'main.php?page=um_browse', 'something is wrong');
		exit;
	}
	
	jump_to(URL_ADMIN.'main.php?page=um_browse');
?>