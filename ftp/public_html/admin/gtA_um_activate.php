<?php
	include_once '_connect.php';
	define('ACCESS_LEVEL', 'admin');
	include_once SYS_ALLOW_EXECUTION;
	include_once SYS_USERS;
	include_once PATH_ENGINE.'func_showmsg.php';
	
	//Check POST data
	
	if (
		!isset($_POST['um_selected']) ||
		!isset($_POST['um_mode'])
	) {
		jump_to(URL_ADMIN.'main.php?page=um_browse', 'gtA_um_activate.php: invalid post data');
		exit;
	}
	
	user_activate($_POST['um_selected'], $_POST['um_mode']);
	
	jump_to(URL_ADMIN.'main.php?page=um_browse');
?>